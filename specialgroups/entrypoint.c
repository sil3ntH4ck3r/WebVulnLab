#define _GNU_SOURCE
#include <unistd.h>
#include <sys/types.h>
#include <stdio.h>
#include <stdlib.h>

int main(int argc, char **argv) {
    if (setgid(0) != 0 || setuid(0) != 0) {
        perror("setuid/setgid");
        return 1;
    }
    char *const args[] = { "/usr/bin/tini", "-g", "--", "/usr/local/sbin/start-incus.sh", NULL };
    execv(args[0], args);
    perror("execv");
    return 1;
}