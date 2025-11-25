#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define TITLE_LEN 80

typedef struct {
    char title[TITLE_LEN];
    int done;  // 0 - не выполнено, 1 - выполнено
} Task;

void clearInput(void) {
    int c;
    while ((c = getchar()) != '\n' && c != EOF) { }
}

void printMenu(void) {
    printf("\n=== Менеджер задач ===\n");
    printf("1) Добавить задачу\n");
    printf("2) Показать задачи\n");
    printf("3) Отметить задачу как выполненную\n");
    printf("4) Удалить задачу\n");
    printf("0) Выход\n");
    printf("Выберите пункт: ");
}

void printTasks(Task *tasks, int count) {
    if (count == 0) {
        printf("\nСписок задач пуст.\n");
        return;
    }

    printf("\nСписок задач:\n");
    for (int i = 0; i < count; i++) {
        printf("%d) [%c] %s\n",
               i + 1,
               tasks[i].done ? 'X' : ' ',
               tasks[i].title);
    }
}

void addTask(Task **tasks, int *count) {
    if (!tasks || !count) return;

    Task *newArr = realloc(*tasks, (*count + 1) * sizeof(Task));
    if (!newArr) {
        printf("Ошибка: не удалось выделить память.\n");
        return;
    }

    *tasks = newArr;

    printf("Введите текст задачи (макс %d символов): ", TITLE_LEN - 1);
    clearInput();
    if (!fgets((*tasks)[*count].title, TITLE_LEN, stdin)) {
        printf("Ошибка ввода.\n");
        return;
    }

    // Удаляем '\n' в конце строки, если есть
    size_t len = strlen((*tasks)[*count].title);
    if (len > 0 && (*tasks)[*count].title[len - 1] == '\n') {
        (*tasks)[*count].title[len - 1] = '\0';
    }

    (*tasks)[*count].done = 0;
    (*count)++;

    printf("Задача добавлена.\n");
}

void markTaskDone(Task *tasks, int count) {
    if (count == 0) {
        printf("Нет задач для изменения.\n");
        return;
    }

    int index;
    printf("Введите номер задачи для отметки: ");
    if (scanf("%d", &index) != 1) {
        printf("Некорректный ввод.\n");
        clearInput();
        return;
    }

    if (index < 1 || index > count) {
        printf("Задачи с таким номером нет.\n");
        return;
    }

    tasks[index - 1].done = 1;
    printf("Задача №%d отмечена как выполненная.\n", index);
}

void deleteTask(Task **tasks, int *count) {
    if (*count == 0) {
        printf("Нет задач для удаления.\n");
        return;
    }

    int index;
    printf("Введите номер задачи для удаления: ");
    if (scanf("%d", &index) != 1) {
        printf("Некорректный ввод.\n");
        clearInput();
        return;
    }

    if (index < 1 || index > *count) {
        printf("Задачи с таким номером нет.\n");
        return;
    }

    int pos = index - 1;
    for (int i = pos; i < *count - 1; i++) {
        (*tasks)[i] = (*tasks)[i + 1];
    }

    Task *newArr = realloc(*tasks, (*count - 1) * sizeof(Task));
    if (newArr || *count - 1 == 0) {
        *tasks = newArr;
    }

    (*count)--;
    printf("Задача удалена.\n");
}

int main(void) {
    Task *tasks = NULL;
    int taskCount = 0;
    int choice;

    for (;;) {
        printMenu();
        if (scanf("%d", &choice) != 1) {
            printf("Некорректный ввод.\n");
            clearInput();
            continue;
        }

        switch (choice) {
            case 1:
                addTask(&tasks, &taskCount);
                break;
            case 2:
                printTasks(tasks, taskCount);
                break;
            case 3:
                printTasks(tasks, taskCount);
                markTaskDone(tasks, taskCount);
                break;
            case 4:
                printTasks(tasks, taskCount);
                deleteTask(&tasks, &taskCount);
                break;
            case 0:
                printf("Выход...\n");
                free(tasks);
                return 0;
            default:
                printf("Неизвестный пункт меню.\n");
        }
    }
}



int main() {
  // output a line
  printf("Hello World!\n");
}
