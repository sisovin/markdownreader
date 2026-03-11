# MarkdownReader

A lightweight **Markdown book reader and editor platform** designed for developers, writers, and educators who want to manage Markdown documents easily.

MarkdownReader allows users to **read, edit, preview, and manage Markdown-based books** directly from a web interface while keeping all content stored as simple `.md` files.

---

## Features

* Markdown book reader
* Web-based Markdown editor
* Live preview rendering
* Image asset manager
* Filesystem-based storage
* Lightweight routing system
* Clean reader interface
* Admin dashboard for content management

---

## Project Goals

MarkdownReader was created to provide a **simple alternative to heavy documentation systems**.

The project focuses on:

* Simplicity
* Performance
* Maintainability
* Author productivity

Instead of relying on a database, MarkdownReader stores documents as **plain Markdown files**, making it easy to manage content using Git.

---

## Architecture

MarkdownReader follows a **modular MVC-style architecture**.

```
Client Browser
      │
      ▼
Routing System
      │
      ▼
Controllers
      │
      ▼
Models / Services
      │
      ▼
Markdown Renderer
      │
      ▼
HTML Views
```

Main components include:

* Router
* Controllers
* Markdown Renderer
* Document Manager
* Asset Manager

---

## Project Structure

```
markdownreader/
│
├── app/
│   ├── Controllers
│   ├── Models
│   ├── Core
│   └── Helpers
│
├── config/
│
├── public/
│   ├── markdown/
│   │   ├── books
│   │   └── images
│   └── index.php
│
├── resources/
│   ├── views
│   └── css
│
└── docs/
```

---

## Markdown Storage

All Markdown documents are stored in:

```
public/markdown/
```

Example structure:

```
markdown/
├── python-book.md
├── web-dev-guide.md
└── images/
    ├── diagram.png
    └── chart.png
```

---

## Example Markdown

```markdown
# Introduction

Welcome to MarkdownReader.

## Chapter 1

Markdown makes writing documentation simple.

![Example Image](images/example.png)
```

---

## Admin Dashboard

The admin dashboard allows administrators to:

* Create and edit Markdown documents
* Preview Markdown rendering
* Upload images
* Manage document assets

Editor layout:

```
+-------------------+-------------------+
| Markdown Editor   | Live Preview      |
|                   |                   |
| Write Markdown    | Rendered HTML     |
+-------------------+-------------------+
```

---

## Live Preview System

MarkdownReader includes a **real-time preview system**.

Workflow:

```
User types Markdown
        │
        ▼
Preview request
        │
        ▼
Markdown Renderer
        │
        ▼
Rendered HTML
        │
        ▼
Preview panel updates
```

---

## Asset Management

Images used in Markdown files are stored in:

```
public/markdown/images/
```

Example Markdown image:

```markdown
![Diagram](images/diagram.png)
```

Admin features include:

* Upload images
* Copy Markdown path
* Delete assets
* Preview uploaded files

---

## Installation

Clone the repository:

```
git clone https://github.com/sisovin/markdownreader.git
```

Navigate to the project directory:

```
cd markdownreader
```

Start your web server and point it to:

```
public/
```

---

## Requirements

Typical environment requirements:

* Web server (Apache / Nginx)
* PHP runtime (if implemented in PHP)
* File system access

---

## Usage

### Reading a Book

Open in browser:

```
/books/bookname.md
```

### Editing a Document

Open the admin dashboard:

```
/admin/dashboard
```

---

## Security Considerations

The system includes several safeguards:

* File upload validation
* Input sanitization
* Path normalization
* CSRF protection for admin actions

---

## Future Improvements

Planned improvements include:

* Full-text search
* Syntax highlighting
* Chapter navigation
* Version history
* Multi-user support
* Markdown export tools

---

## Documentation

Project documentation can be found in the `docs/` directory.

Available documents include:

* Software Design Document
* Architecture diagrams
* Academic report

---

## Contributing

Contributions are welcome.

Steps to contribute:

1. Fork the repository
2. Create a feature branch
3. Submit a pull request

---

## License

This project is released under the MIT License.

---

## Author

Sisovin Chieng

---