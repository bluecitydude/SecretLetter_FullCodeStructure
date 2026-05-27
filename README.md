# SecretLetter

SecretLetter is a lightweight, secure messaging platform built with a clean frontend and a headless backend API. It focuses on zero-storage cryptography: the application encrypts and decrypts data without persisting any sensitive information.

## Overview

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP with OpenSSL
- **Deployment model:** frontend hosted as a static site, backend hosted as a headless API service

This project is built to make encryption easy for users and developers, with a polished UI and a simple JSON API for integration.

## What this project does

- Provides a browser UI for encrypting and decrypting messages
- Uses multi-layer encryption for stronger protection
- Keeps the backend stateless so no message data is stored on the server
- Offers an API endpoint for developers to integrate encryption into their own applications

## Technology Stack

- **Frontend:**
  - HTML for semantic content
  - CSS for responsive cyber-inspired styling
  - JavaScript for form handling, mode switching, and API calls
- **Backend:**
  - PHP for request processing and response handling
  - OpenSSL for encryption and decryption operations
  - Headless API architecture so the backend is dedicated to data processing only

## Project Structure

- `Frontend/Views/` - landing page, app page, API docs, tech stack page
- `Frontend/css/` - styling for the UI
- `Frontend/js/` - JavaScript app behavior and API integration
- `Backend/Controller/Controller.php` - API endpoint logic
- `Backend/Models/Encrypter.php` - encryption logic
- `Backend/Models/Decrypter.php` - decryption logic

## Deployment

This project is designed for separate deployments:

- **Frontend:** can be hosted on Vercel or any static hosting provider
- **Backend:** can be hosted on Render as a headless API service

This split keeps the UI fast and the API secure.

## How to run locally

1. Clone the repository:
   ```bash
   git clone https://github.com/bluecitydude/SecretLetter_FullCodeStructure.git
   ```
2. Place the project in a PHP-enabled local server environment such as XAMPP.
3. Open the `Frontend/Views/index.html` page in your browser, or navigate to the local web server root.
4. Ensure the backend API endpoint is available if you want to use the encryption/decryption app.

## API Usage

The API endpoint accepts JSON requests with these fields:

- `mode`: `encrypt` or `decrypt`
- `message` / `secret`: the text to encrypt or the encrypted payload to decrypt
- `passphrase`: the secret key used for the operation
- `times`: number of encryption/decryption iterations (1-5)

A successful response looks like:

```json
{
  "status": "success",
  "response": "..."
}
```

## Links

- Creator: https://www.linkedin.com/in/tejendra-purohit-14b753311/
- Code: https://github.com/bluecitydude/SecretLetter_FullCodeStructure
- Live: https://secretletter-livid.vercel.app/

## Notes

This repository is designed to demonstrate a simple but powerful encryption workflow with a clean frontend experience and a secure, stateless API backend. It’s intended for educational use and lightweight deployment scenarios.
