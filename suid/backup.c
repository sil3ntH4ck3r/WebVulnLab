#define _POSIX_C_SOURCE 200809L
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <limits.h>
#include <errno.h>
#include <sys/wait.h>
#include <sys/stat.h>
#include <time.h>
#include <pwd.h>

#define MAX_ARGS 256
#define MAX_PATH_LEN 4096
#define LOG_FILE "/var/log/corporate_backup.log"

void write_audit_log(const char *user, const char *operation, const char *target) {
    FILE *log_fp;
    time_t now;
    struct tm *tm_info;
    char timestamp[64];
    
    log_fp = fopen(LOG_FILE, "a");
    if (!log_fp) {
        log_fp = fopen("./backup_audit.log", "a");
    }
    
    if (log_fp) {
        time(&now);
        tm_info = localtime(&now);
        strftime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S", tm_info);
        
        fprintf(log_fp, "[%s] USER:%s OPERATION:%s TARGET:%s\n", 
                timestamp, user, operation, target);
        fclose(log_fp);
    }
}

int validate_archive_name(const char *archive) {
    if (!archive || strlen(archive) == 0) {
        fprintf(stderr, "Error: Nombre de archivo vacío\n");
        return 0;
    }
    
    if (archive[0] == '-') {
        fprintf(stderr, "Error: Nombre de archivo no puede empezar con '-'\n");
        return 0;
    }
    
    if (strlen(archive) > MAX_PATH_LEN - 1) {
        fprintf(stderr, "Error: Nombre de archivo demasiado largo\n");
        return 0;
    }
    
    return 1;
}

void show_corporate_header() {
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);

    printf("=== Secure Unified Integrity‑Data ===\n");
    printf("Enterprise Security & Compliance Edition\n");
    printf("(c) %d All rights reserved\n\n", tm.tm_year + 1900);
}

int main(int argc, char **argv) {
    char *zip_args[MAX_ARGS];
    int arg_count = 0;
    struct passwd *pwd;
    char *username;
    
    show_corporate_header();
    
    if (argc < 2) {
        fprintf(stderr, "Uso: %s <archive.zip> [archivos/directorios...]\n", argv[0]);
        fprintf(stderr, "\nEjemplos:\n");
        fprintf(stderr, "  %s backup.zip /etc/hosts /var/log/nginx\n", argv[0]);
        fprintf(stderr, "  %s daily_backup.zip /home/user/documents\n", argv[0]);
        return 2;
    }
    
    pwd = getpwuid(getuid());
    username = pwd ? pwd->pw_name : "unknown";
    
    if (!validate_archive_name(argv[1])) {
        write_audit_log(username, "VALIDATION_FAILED", argv[1]);
        return 2;
    }
    
    write_audit_log(username, "BACKUP_START", argv[1]);
    
    printf("Iniciando backup corporativo...\n");
    printf("Usuario: %s\n", username);
    printf("Archivo destino: %s\n", argv[1]);

    setuid(0);
    
    zip_args[arg_count++] = "zip";
    zip_args[arg_count++] = "-r";
    
    for (int i = 1; i < argc && arg_count < MAX_ARGS - 1; i++) {
        zip_args[arg_count++] = argv[i];
    }
    zip_args[arg_count] = NULL;
    
    printf("Ejecutando compresión");
    for (int i = 0; i < 3; i++) {
        printf(".");
        fflush(stdout);
        sleep(1);
    }
    printf("\n\n");
    
    pid_t pid = fork();
    if (pid < 0) {
        perror("Error en fork");
        write_audit_log(username, "SYSTEM_ERROR", "fork_failed");
        return 1;
    } else if (pid == 0) {
        execvp("zip", zip_args);
        perror("Error ejecutando zip");
        _exit(127);
    } else {
        int status;
        if (waitpid(pid, &status, 0) < 0) {
            perror("Error esperando proceso hijo");
            write_audit_log(username, "SYSTEM_ERROR", "waitpid_failed");
            return 1;
        }
        
        int exit_code = WIFEXITED(status) ? WEXITSTATUS(status) : 1;
        
        if (exit_code == 0) {
            printf("✓ Backup completado exitosamente\n");
            printf("✓ Archivo creado: %s\n", argv[1]);
            printf("✓ Operación registrada en log de auditoría\n");
            write_audit_log(username, "BACKUP_SUCCESS", argv[1]);
        } else {
            printf("✗ Error durante la compresión (código: %d)\n", exit_code);
            write_audit_log(username, "BACKUP_FAILED", argv[1]);
        }
        
        printf("\n=== Fin de operación ===\n");
        return exit_code;
    }
}