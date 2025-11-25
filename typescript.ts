let petOnSale = 'chinchilla';
let ordersArray = [
  ['rat', 2], 
  ['chinchilla', 1], 
  ['hamster', 2], 
  ['chinchilla', 50]
];

// Write your code below:
enum Pet {
  Hamster = 'HAMSTER',
  Rat = 'RAT',
  Chinchilla = 'CHINCHILLA',
  Tarantula = 'TARANTULA'
}

const petOnSaleTS : Pet = Pet.Chinchilla;

let ordersArrayTS : [Pet, number][] = [
  [Pet.Rat, 2], 
  [Pet.Chinchilla, 1], 
  [Pet.Hamster, 2], 
  [Pet.Chinchilla, 50]
];

ordersArrayTS.push(['HAMSTER', 1]);

// tasks.ts
import * as fs from "fs";
import * as path from "path";
import * as readline from "readline";

const TASKS_FILE = path.join(__dirname, "tasks.json");

interface Task {
  title: string;
  done: boolean;
}

class TaskManager {
  private tasks: Task[] = [];

  constructor() {
    this.load();
  }

  private load(): void {
    try {
      if (!fs.existsSync(TASKS_FILE)) {
        this.tasks = [];
        return;
      }
      const raw = fs.readFileSync(TASKS_FILE, "utf-8");
      const data = JSON.parse(raw);
      if (Array.isArray(data)) {
        this.tasks = data.map((item: any) => ({
          title: String(item.title ?? ""),
          done: Boolean(item.done),
        }));
      } else {
        this.tasks = [];
      }
    } catch (err) {
      console.log("Предупреждение: файл задач повреждён, начинаем с пустого списка.");
      this.tasks = [];
    }
  }

  private save(): void {
    fs.writeFileSync(TASKS_FILE, JSON.stringify(this.tasks, null, 2), "utf-8");
  }

  public addTask(title: string): void {
    this.tasks.push({ title, done: false });
    this.save();
    console.log("Задача добавлена.");
  }

  public listTasks(): void {
    if (this.tasks.length === 0) {
      console.log("\nСписок задач пуст.");
      return;
    }
    console.log("\nСписок задач:");
    this.tasks.forEach((task, index) => {
      const mark = task.done ? "X" : " ";
      console.log(`${index + 1}) [${mark}] ${task.title}`);
    });
  }

  public markDone(index: number): void {
    if (index < 0 || index >= this.tasks.length) {
      console.log("Задачи с таким номером нет.");
      return;
    }
    this.tasks[index].done = true;
    this.save();
    console.log("Задача отмечена как выполненная.");
  }

  public deleteTask(index: number): void {
    if (index < 0 || index >= this.tasks.length) {
      console.log("Задачи с таким номером нет.");
      return;
    }
    const removed = this.tasks.splice(index, 1)[0];
    this.save();
    console.log(`Задача «${removed.title}» удалена.`);
  }

  public clearDone(): void {
    const before = this.tasks.length;
    this.tasks = this.tasks.filter((t) => !t.done);
    const removed = before - this.tasks.length;
    this.save();
    console.log(`Удалено выполненных задач: ${removed}`);
  }
}

function printMenu(): void {
  console.log("\n=== Менеджер задач (TypeScript) ===");
  console.log("1) Добавить задачу");
  console.log("2) Показать задачи");
  console.log("3) Отметить задачу как выполненную");
  console.log("4) Удалить задачу");
  console.log("5) Удалить все выполненные задачи");
  console.log("0) Выход");
  process.stdout.write("Выберите пункт: ");
}

function ask(rl: readline.Interface, question: string): Promise<string> {
  return new Promise((resolve) => {
    rl.question(question, (answer) => resolve(answer.trim()));
  });
}

async function main(): Promise<void> {
  const manager = new TaskManager();

  const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
  });

  while (true) {
    printMenu();
    const choice = (await ask(rl, "")).trim();

    switch (choice) {
      case "1": {
        const title = await ask(rl, "Введите текст задачи: ");
        if (title) {
          manager.addTask(title);
        } else {
          console.log("Пустая задача не добавлена.");
        }
        break;
      }
      case "2":
        manager.listTasks();
        break;
      case "3": {
        manager.listTasks();
        const num = await ask(rl, "Введите номер задачи для отметки: ");
        const index = Number(num) - 1;
        if (!Number.isNaN(index)) {
          manager.markDone(index);
        } else {
          console.log("Некорректный ввод.");
        }
        break;
      }
      case "4": {
        manager.listTasks();
        const num = await ask(rl, "Введите номер задачи для удаления: ");
        const index = Number(num) - 1;
        if (!Number.isNaN(index)) {
          manager.deleteTask(index);
        } else {
          console.log("Некорректный ввод.");
        }
        break;
      }
      case "5":
        manager.clearDone();
        break;
      case "0":
        console.log("Выход...");
        rl.close();
        return;

