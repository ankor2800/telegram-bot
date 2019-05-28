# Telegram bot

## Stack

- php >=7.1, lumen 5.4

## Description

- отправляет текстовые сообщения
- оправляет изображения
- наклыдывает текст на изображение и отправляет получившееся изображение

## Install

Склонировать код проекта
```
git clone git@github.com:ankor2800/telegram-bot.git .
```
Установка зависимостей:
```
composer install
```

## Linter
Проверка всего проекта
```
composer lint
```
Исправить файлы проекта под правила линтера
```
composer fix
```

## ENV
```
# настройки изображения
IMG_DEBUG # отправляемое изображение
IMG_SAVE_FOLDER # путь к директории куда сохраняем изображения  
IMG_REMOVE # true|false удалять отправленное сообщение 

# настройки текста накладываемого на изображение
TEXT_BG_COLOR # цвет подложки под текст
TEXT_FONT # путь к файлу шрифта
TEXT_FONT_SIZE # размер текста
TEXT_FONT_COLOR # цвет текста
TEXT_PADDING # отступы текста от краев изображения (px)

# настройки бота
T_BOT_TOKEN # токен к api telegram бота
T_CHAT_ID # chat id куда отправлять сообщения
USE_SOCKS5_PROXY # true|false используем прокси в запросах к API telegram
SOCKS5_PROXY # socks5 proxy
```
## Controllers

### /bot/check/
#### GET
##### Description:

Запрос для проверки токена авторизации бота

##### Responses

| Code | Description | API |
| ---- | ----------- | ------ |
| 200 | Ответ от API Telegram | https://tlgrm.ru/docs/bots/api#getme |

### /bot/updates/
#### GET
##### Description:

Запрос на получение обновлений чата

##### Responses

| Code | Description | API |
| ---- | ----------- | ------ |
| 200 | Ответ от API Telegram | https://tlgrm.ru/docs/bots/api#getupdates |

### /bot/send/message

#### PUT
##### Description:

Запрос на оптавку текстового сообщения

##### Parameters

| Name | Located in | Description | Required | Schema |
| ---- | ---------- | ----------- | -------- | ---- |
| text | query | Текст сообщения | Yes | string |

### /bot/send/image

#### PUT
##### Description:

Запрос на оптавку изображения.

##### Parameters

| Name | Located in | Description | Required | Schema |
| ---- | ---------- | ----------- | -------- | ---- |
| text | query | Текст накладываемый на изображение | No | string |
