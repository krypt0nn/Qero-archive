[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KRypt0nn/Qero/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/KRypt0nn/Qero/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/KRypt0nn/Qero/badges/build.png?b=master)](https://scrutinizer-ci.com/g/KRypt0nn/Qero/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/KRypt0nn/Qero/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![License](https://badges.frapsoft.com/os/gpl/gpl.png?v=103)](https://www.gnu.org/licenses/gpl-3.0.html)

# Qero
Qero - пакетный менеджер для **PHP**, представленный в виде распространяемого **phar** архива

## Сборка
Для сборки вам достаточно прописать команду в командной строке

```cmd
php build.php
```

находясь в основной директории проекта. При этом на компьютере должен быть предустановлен **PHP** версии **5.3** и выше

> Если команда не запускается, пропишите полный путь до исполняемого **PHP** файла

После выполнения команды создастся файл **Qero.phar** - главный и единственный файл проекта, а в консоль будет выведена различная информация о сборке

## Работа с Qero
Работа с **Qero**, как и с любыми другими **phar** архивами, может проходить как через командную консоль, так и через **PHP** код

Для просмотра списка команд вы можете вызвать:

```cmd
php Qero.phar
```

или

```cmd
php Qero.phar help
```

### Возможные команды:

Команда | Аргументы | Описание
--------|-----------|----------
**help** | - | Вывод список доступных команд
**install** | [список репозиториев] | Установка пакетов
**delete** | [список пакетов] | Удаление пакетов *(с указанием источника пакета)*
**update** | - | Обновление *(переустановка)* пакетов
**packages** | - | Вывод списка установленных пакетов

К примеру:

```cmd
php Qero.phar install KRypt0nn/TreeStructure KRypt0nn/Dataset-Structures
```

Имена пакетов так же могут быть дополнены источником - одной из следующих фраз

* **github**
* **gitlab**
* **bitbucket**

в начале название через двоеточие. К примеру, если у вас есть пакет в сервисе **GitLab** *(на примере **KRypt0n_/MathLib**)*, то для его подключения необходимо указать **gitlab:KRypt0n_/MathLib**

```cmd
php Qero.phar install gitlab:KRypt0n_/MathLib
```

### Запуск через PHP

Для работы с **Qero** через **PHP** файл вы должны использовать класс **PackagesManager**. Подробную документацию я опущу, однако вот конкретные примеры:

```php
<?php

require 'Qero.phar';

use Qero\PackagesManager\PackagesManager;

$manager = new PackagesManager;
$manager->installPackage ('KRypt0nn/TreeStructure');

require 'qero-packages/autoload.php';

$tree = new Tree;
// и т.д.
```

Соответственно, файл **Qero.phar** должен лежать рядом с исполняемым **PHP** файлом

А вот обновление всех установленных пакетов:

```php
<?php

require 'Qero.phar';

use Qero\PackagesManager\PackagesManager;

$manager = new PackagesManager;
$manager->updatePackages ();
```

> К слову, **Qero** работает с директорией, в которой находится сам его главный файл, или с той, где этот самый файл был вызван, так что у каждой директории пакеты будут свои. Будьте внимательны

### Минимальные требования

Для работы проекта необходим **PHP** версии **5.3** и выше. Проект может работать как с **CURL**, так и через основные функции **PHP**. Однако стоит так же иметь в сборке расширение **OpenSSL** и работать с **PHP** более новых версий, т.к. в старых версиях *(в т.ч. **5.3**)* есть проблемы с **SSL**

## Создание Qero пакета
**Qero** работает с **GitHub**, **GitLab** и **BitBucket**. Для создания своего пакета вам нужно лишь создать репозиторий в одном из этих сервисов. Путь до вашего репозитория в адресной строке - и есть путь для установки через **Qero**

> Учтите, что если вы используете не **GitHub**, то вы так же должны указать источник пакета

**Qero** будет автоматически подключать файлы из главной директории репозитория со следующими названиями *(в порядке понижения приоритета)*:

* **[название репозитория пакета].php**
* **qero-init.php**
* **qero-main.php**
* **main.php**
* **index.php**
* **autorun.php**
* **startup.php**

Вы так же можете указать настройки для установки вашего пакета. Для этого создайте файл **qero-info.json** в корневой директории вашего репозитория. В этом файле вы можете прописать главную информацию для корректной работы **Qero**

> Если этого файла нет, то **Qero** будет пытаться сделать всё за вас, и в больших проектах это может привести не к самым лучшим последствиям

### Доступные настройки:

Название | Описание
---------|---------
**version** | Версия пакета *(в будущем - пригодится!.. надеюсь)*
**entry_point** | Точка входа пакета - **PHP** файл, который будет подключен автоматически
**requires** | Список зависимостей пакета. Они будут установлены вместе с пакетом и запущены до него

К примеру:

```json
{
    "version": "1.0",
    "entry_point": "packet.php",
    "requires": [
        "KRypt0nn/TreeStructure",
        "KRypt0nn/Dataset-Structures"
    ]
}
```

Вот и всё. Приятного использования! :3

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](http://vk.com/hphp_convertation)