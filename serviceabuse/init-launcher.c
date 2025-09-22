#define _GNU_SOURCE
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
int main(void) {
    if (setgid(0) != 0) { perror("setgid"); return 1; }
    if (setuid(0) != 0) { perror("setuid"); return 1; }
    char *const argv[] = { "/sbin/init", NULL };
    char *const envp[] = { "container=docker", NULL };
    execve("/sbin/init", argv, envp);
    perror("execve /sbin/init");
    return 1;
}
