// backup.c
#define _POSIX_C_SOURCE 200809L
#include <errno.h>
#include <limits.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <time.h>
#include <unistd.h>

static void die(const char *msg) {
    perror(msg);
    exit(EXIT_FAILURE);
}

static bool dir_exists(const char *path) {
    struct stat st;
    if (stat(path, &st) != 0) return false;
    return S_ISDIR(st.st_mode);
}

int main(void) {
    setuid(0);
    const char *DEST = "/backup";
    puts("Iniciando backup del sistema...");
    
    /* Comprobar root (equivale a (( EUID == 0 )) ) */
    if (geteuid() != 0) {
        fprintf(stderr, "ERROR: ejecute como root (sudo)\n");
        return EXIT_FAILURE;
    }
    
    /* Comprobar que DEST existe y es directorio y escribible */
    if (!dir_exists(DEST)) {
        fprintf(stderr, "ERROR: %s no existe o no es un directorio\n", DEST);
        return EXIT_FAILURE;
    }
    if (access(DEST, W_OK) != 0) {
        fprintf(stderr, "ERROR: No puedo escribir en %s: %s\n", DEST, strerror(errno));
        return EXIT_FAILURE;
    }
    
    /* Generar timestamp estilo %F_%H%M%S */
    time_t t = time(NULL);
    struct tm lt;
    if (!localtime_r(&t, &lt)) die("localtime_r");
    char ts[32];
    if (strftime(ts, sizeof ts, "%F_%H%M%S", &lt) == 0) {
        fprintf(stderr, "ERROR: no pude formatear la fecha\n");
        return EXIT_FAILURE;
    }
    
    /* Ruta de salida: /backup/system_backup_<TS>.tar.gz */
    char out[PATH_MAX];
    int n = snprintf(out, sizeof out, "%s/system_backup_%s.tar.gz", DEST, ts);
    if (n < 0 || (size_t)n >= sizeof out) {
        fprintf(stderr, "ERROR: ruta de salida demasiado larga\n");
        return EXIT_FAILURE;
    }
    
    /* VULNERABLE: No establecer PATH seguro - usar el PATH del usuario */
    umask(022);
    
    /* Ejecutar: tar -C / -czf <out> home */
    pid_t pid = fork();
    if (pid < 0) die("fork");
    if (pid == 0) {
        /* Proceso hijo: reemplazar por tar (sin pasar por /bin/sh) */
        execlp("tar", "tar", "-C", "/", "-czf", out, "home", (char *)NULL);
        /* Si llegamos aquí, execlp falló */
        perror("execlp(tar)");
        _exit(127);
    }
    
    int status = 0;
    if (waitpid(pid, &status, 0) < 0) die("waitpid");
    if (!WIFEXITED(status) || WEXITSTATUS(status) != 0) {
        fprintf(stderr, "ERROR: tar falló (status=%d)\n", status);
        return EXIT_FAILURE;
    }
    
    printf("Backup completado: %s\n", out);
    return EXIT_SUCCESS;
}