#define _GNU_SOURCE
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/types.h>
#include <errno.h>
#include <time.h>
#include <stdint.h>
#define ROTR(x,n) (((x) >> (n)) | ((x) << (32-(n))))
#define CH(x,y,z)  (((x) & (y)) ^ (~(x) & (z)))
#define MAJ(x,y,z) (((x) & (y)) ^ ((x) & (z)) ^ ((y) & (z)))
#define SIG0(x) (ROTR((x), 2) ^ ROTR((x),13) ^ ROTR((x),22))
#define SIG1(x) (ROTR((x), 6) ^ ROTR((x),11) ^ ROTR((x),25))
#define sig0(x) (ROTR((x), 7) ^ ROTR((x),18) ^ ((x) >> 3))
#define sig1(x) (ROTR((x),17) ^ ROTR((x),19) ^ ((x) >> 10))

static const uint32_t K256[64] = {
  0x428a2f98,0x71374491,0xb5c0fbcf,0xe9b5dba5,0x3956c25b,0x59f111f1,0x923f82a4,0xab1c5ed5,
  0xd807aa98,0x12835b01,0x243185be,0x550c7dc3,0x72be5d74,0x80deb1fe,0x9bdc06a7,0xc19bf174,
  0xe49b69c1,0xefbe4786,0x0fc19dc6,0x240ca1cc,0x2de92c6f,0x4a7484aa,0x5cb0a9dc,0x76f988da,
  0x983e5152,0xa831c66d,0xb00327c8,0xbf597fc7,0xc6e00bf3,0xd5a79147,0x06ca6351,0x14292967,
  0x27b70a85,0x2e1b2138,0x4d2c6dfc,0x53380d13,0x650a7354,0x766a0abb,0x81c2c92e,0x92722c85,
  0xa2bfe8a1,0xa81a664b,0xc24b8b70,0xc76c51a3,0xd192e819,0xd6990624,0xf40e3585,0x106aa070,
  0x19a4c116,0x1e376c08,0x2748774c,0x34b0bcb5,0x391c0cb3,0x4ed8aa4a,0x5b9cca4f,0x682e6ff3,
  0x748f82ee,0x78a5636f,0x84c87814,0x8cc70208,0x90befffa,0xa4506ceb,0xbef9a3f7,0xc67178f2
};

static void sha256_transform(uint32_t H[8], const uint8_t block[64]) {
    uint32_t W[64];
    for (int t=0; t<16; t++) {
        W[t] = (block[t*4]<<24) | (block[t*4+1]<<16) | (block[t*4+2]<<8) | (block[t*4+3]);
    }
    for (int t=16; t<64; t++) {
        W[t] = sig1(W[t-2]) + W[t-7] + sig0(W[t-15]) + W[t-16];
    }
    uint32_t a=H[0],b=H[1],c=H[2],d=H[3],e=H[4],f=H[5],g=H[6],h=H[7];
    for (int t=0; t<64; t++) {
        uint32_t T1 = h + SIG1(e) + CH(e,f,g) + K256[t] + W[t];
        uint32_t T2 = SIG0(a) + MAJ(a,b,c);
        h=g; g=f; f=e; e=d + T1; d=c; c=b; b=a; a=T1 + T2;
    }
    H[0]+=a; H[1]+=b; H[2]+=c; H[3]+=d; H[4]+=e; H[5]+=f; H[6]+=g; H[7]+=h;
}

static void sha256(const uint8_t *msg, size_t len, uint8_t out[32]) {
    uint32_t H[8] = {
      0x6a09e667,0xbb67ae85,0x3c6ef372,0xa54ff53a,
      0x510e527f,0x9b05688c,0x1f83d9ab,0x5be0cd19
    };
    uint8_t block[64];
    size_t i = 0;
    while (len - i >= 64) {
        memcpy(block, msg + i, 64);
        sha256_transform(H, block);
        i += 64;
    }
    size_t rem = len - i;
    memset(block, 0, 64);
    memcpy(block, msg + i, rem);
    block[rem] = 0x80;
    if (rem >= 56) {
        sha256_transform(H, block);
        memset(block, 0, 64);
    }
    uint64_t bitlen = (uint64_t)len * 8;
    for (int j=0; j<8; j++) block[56+7-j] = (uint8_t)((bitlen >> (j*8)) & 0xff);
    sha256_transform(H, block);
    for (int j=0; j<8; j++) {
        out[j*4+0] = (uint8_t)(H[j] >> 24);
        out[j*4+1] = (uint8_t)(H[j] >> 16);
        out[j*4+2] = (uint8_t)(H[j] >>  8);
        out[j*4+3] = (uint8_t)(H[j] >>  0);
    }
}

static void to_hex(const uint8_t *in, size_t n, char *out_hex, size_t out_sz) {
    static const char *hexd = "0123456789abcdef";
    size_t need = n*2 + 1;
    if (out_sz < need) return;
    for (size_t i=0;i<n;i++) {
        out_hex[i*2+0] = hexd[(in[i]>>4)&0xf];
        out_hex[i*2+1] = hexd[in[i]&0xf];
    }
    out_hex[n*2] = '\0';
}

/* =========================
   Servicio "realista"
   ========================= */

__attribute__((noinline))
void banner(void) {
    puts("=========================================");
    puts(" Admin Service v1.6  (console interface) ");
    puts("=========================================");
}

struct creds { char user[32]; char pass[32]; };

__attribute__((noinline))
void read_field(const char *prompt, char *buf) {
    printf("%s", prompt);
    fflush(stdout);
    ssize_t n = read(STDIN_FILENO, buf, 256);
    if (n < 0) { perror("read"); exit(1); }
}

__attribute__((noinline))
void audit_log(const char *user, int success) {
    FILE *f = fopen("/var/log/adminsvc.log", "a");
    if (!f) return;
    char line[64];
    time_t t = time(NULL);
    struct tm *tm = localtime(&t);
    char ts[32];
    strftime(ts, sizeof(ts), "%Y-%m-%d %H:%M:%S", tm);
    sprintf(line, "[%s] login user=%s result=%s", ts, user, success ? "OK" : "FAIL");
    fputs(line, f); fputc('\n', f); fclose(f);
}

static int verify_password_sha256(const char *pass) {
    // echo -n 'sup3rs3cr3tp4$$w0rd' | sha256sum
    const char *expected_hex = "c68119516acdf849d903b98cc31e19ba8228a79e0503460a77e0a83e53278279";

    uint8_t h[32]; char hex[65];
    sha256((const uint8_t*)pass, strlen(pass), h);
    to_hex(h, 32, hex, sizeof(hex));
    return strncmp(hex, expected_hex, 64) == 0;
}

__attribute__((noinline))
int authenticate(struct creds *c) {
    int ok_user = (strncmp(c->user, "admin", 64) == 0);
    int ok_pass = verify_password_sha256(c->pass);
    int ok = ok_user && ok_pass;
    audit_log(c->user, ok);
    return ok;
}

__attribute__((noinline))
void admin_console(void) {
    puts("[admin] Bienvenido. Opciones disponibles:");
    puts(" 1) Ver estado del servicio");
    puts(" 2) Salir");
    printf("> ");
    char opt[8];
    read(STDIN_FILENO, opt, 32);
    puts("Estado: OK (simulado).");
}

int main(int argc, char **argv) {
    if (setuid(0) != 0) { perror("setuid"); return 1; }
    if (setgid(0) != 0) { perror("setgid"); return 1; }

    banner();

    struct creds c; memset(&c, 0, sizeof(c));
    read_field("Usuario: ", c.user);
    read_field("Contraseña: ", c.pass);

    if (authenticate(&c)) { puts("[+] Autenticación correcta."); admin_console(); }
    else { puts("[-] Credenciales inválidas."); }
    puts("Hasta pronto.");
    return 0;
}
