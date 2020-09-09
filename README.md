
## Setting Up
Clone the repo using this link
```bash
git clone https://github.com/ajosav/sendmailsviaapi
```


After cloning the repo, run the fillowing commands

```bash
    composer install
```

// Create a .env file and run
```bash
**php artisan key:generate**
```


## Endpoint
**Request Type: POST**
https://domain.com/api/v1/send


### API Authentication

```bash
x-api-key: ''
```

### Query Parameters
```bash
recipient: required|string|array,
subject: required|string,
content: required|string,
cc: optional|string|array,
attachment: optional|file|ArrayFile,
```