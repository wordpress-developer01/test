<?php
// tasks.php

const TASKS_FILE = __DIR__ . '/tasks.json';

class Task
{
    public string $title;
    public bool $done;

    public function __construct(string $title, bool $done = false)
    {
        $this->title = $title;
        $this->done  = $done;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'done'  => $this->done,
        ];
    }

    public static function fromArray(array $data): Task
    {
        $title = isset($data['title']) ? (string)$data['title'] : '';
        $done  = isset($data['done']) ? (bool)$data['done'] : false;
        return new Task($title, $done);
    }
}

class TaskManager
{
    /** @var Task[] */
    private array $tasks = [];

    public function __construct()
    {
        $this->load();
    }

    private function load(): void
    {
        if (!file_exists(TASKS_FILE)) {
            $this->tasks = [];
            return;
        }

        $raw = file_get_contents(TASKS_FILE);
        if ($raw === false) {
            $this->tasks = [];
            return;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            echo "Предупреждение: файл задач повреждён. Запускаем с пустым списком.\n";
            $this->tasks = [];
            return;
        }

        $this->tasks = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $this->tasks[] = Task::fromArray($item);
            }
        }
    }

    private function save(): void
    {
        $array = [];
        foreach ($this->tasks as $task) {
            $array[] = $task->toArray();
        }

        file_put_contents(
            TASKS_FILE,
            json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    public function addTask(string $title): void
    {
        $this->tasks[] = new Task($title);
        $this->save();
        echo "Задача добавлена.\n";
    }

    public function listTasks(): void
    {
        if (empty($this->tasks)) {
            echo "\nСписок задач пуст.\n";
            return;
        }

        echo "\nСписок задач:\n";
        foreach ($this->tasks as $index => $task) {
            $mark = $task->done ? 'X' : ' ';
            $num  = $index + 1;
            echo "{$num}) [{$mark}] {$task->title}\n";
        }
    }

    public function markDone(int $index): void
    {
        if (!isset($this->tasks[$index])) {
            echo "Задачи с таким номером нет.\n";
            return;
        }

        $this->tasks[$index]->done = true;
        $this->save();
        echo "Задача отмечена как выполненная.\n";
    }

    public function deleteTask(int $index): void
    {
        if (!isset($this->tasks[$index])) {
            echo "Задачи с таким номером нет.\n";
            return;
        }

        $removed = $this->tasks[$index]->title;
        array_splice($this->tasks, $index, 1);
        $this->save();
        echo "Задача «{$removed}» удалена.\n";
    }

    public function clearDone(): void
    {
        $before = count($this->tasks);
        $this->tasks = array_values(array_filter(
            $this->tasks,
            fn (Task $t) => !$t->done
        ));
        $removed = $before - count($this->tasks);
        $this->save();
        echo "Удалено выполненных задач: {$removed}\n";
    }
}

function printMenu(): void
{
    echo "\n=== Менеджер задач (PHP) ===\n";
    echo "1) Добавить задачу\n";
    echo "2) Показать задачи\n";
    echo "3) Отметить задачу как выполненную\n";
    echo "4) Удалить задачу\n";
    echo "5) Удалить все выполненные задачи\n";
    echo "0) Выход\n";
    echo "Выберите пункт: ";
}

function readLine(string $prompt = ''): string
{
    if ($prompt !== '') {
        echo $prompt;
    }
    $line = fgets(STDIN);
    return $line === false ? '' : trim($line);
}

function main(): void
{
    $manager = new TaskManager();

    while (true) {
        printMenu();
        $choice = readLine();

        switch ($choice) {
            case '1':
                $title = readLine("Введите текст задачи: ");
                if ($title !== '') {
                    $manager->addTask($title);
                } else {
                    echo "Пустая задача не добавлена.\n";
                }
                break;

            case '2':
                $manager->listTasks();
                break;

            case '3':
                $manager->listTasks();
                $num = readLine("Введите номер задачи для отметки: ");
                if (ctype_digit($num)) {
                    $manager->markDone(((int)$num) - 1);
                } else {
                    echo "Некорректный ввод.\n";
                }
                break;

            case '4':
                $manager->listTasks();
                $num = readLine("Введите номер задачи для удаления: ");
                if (ctype_digit($num)) {
                    $manager->deleteTask(((int)$num) - 1);
                } else {
                    echo "Некорректный ввод.\n";
                }
                break;

            case '5':
                $manager->clearDone();
                break;

            case '0':
                echo "Выход...\n";
                return;

            default:
                echo "Неизвестный пункт меню.\n";
        }
    }
}

main();
?>

<?php
// Full name: John Smith
// Fun fact: I can solve a Rubik's cube in under 30 seconds.

// First initial: A
echo "  A  \n";
echo " A A \n";
echo "AAAAA\n";
echo "A   A\n";
echo "A   A\n";
echo "A   A\n";
echo "A   A\n";

echo "\n";

// Second initial: B
echo "BBBB \n";
echo "B   B\n";
echo "B   B\n";
echo "BBBB \n";
echo "B   B\n";
echo "B   B\n";
echo "BBBB \n";
?>
