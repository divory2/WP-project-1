Laravel installation guide
You can use NPM or Bun, Node to compiile application's frontend

install PHP, Composer on local machine
MacOS:  /bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.4)"

WindowsPowerShell:  # Run as administrator...
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))


Linux:  /bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"



Restart Terminal 

install Laravel installer via composer:
    composer global require laravel/installer


Trivia API
request body should include example:
https://opentdb.com/api.php?amount=10&category=9&difficulty=medium&type=multiple


amount
category
difficulty
type


Category Id:    https://opentdb.com/api_category.php


How to initialize curl:
    curl_init()

How to Execute Curl:
curl_exec()

Decode Json response:
    json_decode()

close curl:
curl_close()