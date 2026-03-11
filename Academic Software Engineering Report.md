# MarkdownReader

## Academic Software Engineering Report

**Course:** Software Engineering
**Project:** MarkdownReader
**Author:** Sisovin Chieng
**Date:** March 2026
**Version:** 1.0

---

# Abstract

MarkdownReader is a lightweight web-based application designed to read, edit, and manage Markdown documents efficiently. Markdown has become a popular markup language for writing technical documentation, books, and articles due to its simplicity and readability. However, many Markdown tools focus either on editing or rendering rather than providing a complete system for document management.

The MarkdownReader system addresses this gap by providing an integrated platform that supports Markdown document management, rendering, live preview editing, and image asset management. The platform follows a modular architecture that separates routing, controllers, rendering services, and storage components.

This report describes the design, architecture, implementation strategy, and evaluation of the MarkdownReader system. The system emphasizes simplicity, performance, and maintainability by using filesystem-based storage instead of database-driven content management.

The results demonstrate that MarkdownReader can efficiently manage and render Markdown documents while maintaining a clean and extensible architecture suitable for technical documentation and digital publishing platforms.

---

# Chapter 1 – Introduction

## 1.1 Background

Markdown is a lightweight markup language created to simplify the process of writing formatted text using plain text syntax. It has become widely adopted for writing:

* Technical documentation
* Developer guides
* Online books
* Knowledge bases
* README files

Despite the widespread adoption of Markdown, many existing tools either focus on simple editors or static site generators. There remains a need for a lightweight system that allows users to both manage and read Markdown documents dynamically.

MarkdownReader was developed to address this need by providing a simple yet functional Markdown management and reading system.

---

## 1.2 Problem Statement

Many existing Markdown solutions present several challenges:

1. Static site generators require complex build pipelines.
2. Online editors often lack proper document management.
3. Content management systems introduce unnecessary complexity.

These limitations make it difficult for authors and developers to maintain Markdown-based documentation efficiently.

---

## 1.3 Objectives

The primary objectives of this project are:

* Develop a system for reading Markdown documents in a web browser.
* Provide an administrative dashboard for editing Markdown content.
* Implement a live Markdown preview feature.
* Support image asset management for Markdown documents.
* Maintain a lightweight and maintainable architecture.

---

## 1.4 Scope

The MarkdownReader system focuses on:

* Markdown rendering
* Document editing
* Image asset management
* Filesystem-based storage
* Web-based reading interface

The project does not include:

* Database storage
* User authentication systems
* Multi-user collaboration

---

# Chapter 2 – Literature Review

Markdown tools exist in many forms, including editors, documentation platforms, and static site generators.

## 2.1 Markdown Editors

Examples include:

* Typora
* MarkText
* Obsidian

These tools provide rich editing environments but do not offer built-in web publishing capabilities.

---

## 2.2 Static Site Generators

Popular systems include:

* Jekyll
* Hugo
* Gatsby

These platforms generate static websites from Markdown content but require build steps and configuration.

---

## 2.3 Documentation Platforms

Examples include:

* GitBook
* MkDocs
* Docusaurus

These systems provide documentation frameworks but often involve complex configuration.

---

## 2.4 Research Gap

Most existing solutions either:

* Focus on editing Markdown locally, or
* Focus on generating static documentation sites.

MarkdownReader aims to combine both reading and management functionality into a single lightweight system.

---

# Chapter 3 – System Requirements

## 3.1 Functional Requirements

The system must support the following features.

### Document Management

* Create Markdown documents
* Edit Markdown documents
* Save Markdown documents

### Markdown Rendering

* Convert Markdown into HTML
* Display formatted content in a browser

### Live Preview

* Render Markdown dynamically during editing

### Asset Management

* Upload images
* Insert images into Markdown documents
* Delete assets

---

## 3.2 Non-Functional Requirements

### Performance

The system must load Markdown documents quickly and render them efficiently.

### Maintainability

The architecture should allow easy updates and feature extensions.

### Usability

The editor and reader interface should be easy to use.

---

# Chapter 4 – System Architecture

MarkdownReader follows a **layered architecture** composed of several components.

## 4.1 Architecture Layers

The architecture includes:

1. Presentation Layer
2. Application Layer
3. Service Layer
4. Storage Layer

---

### Architecture Overview

Client Browser
↓
Router
↓
Controllers
↓
Models / Services
↓
Markdown Renderer
↓
Filesystem Storage

---

## 4.2 MVC Design Pattern

The system loosely follows the Model–View–Controller architecture.

| Layer      | Responsibility            |
| ---------- | ------------------------- |
| Model      | Handles data storage      |
| View       | Displays HTML output      |
| Controller | Handles application logic |

---

# Chapter 5 – System Design

## 5.1 Routing System

The router handles incoming HTTP requests and maps them to appropriate controllers.

Example routes:

```
/books/{filename}
/admin/dashboard
/admin/documents
/admin/assets
/admin/preview
```

---

## 5.2 Controllers

Controllers coordinate system operations.

Major controllers include:

### Document Controller

Responsible for:

* Loading Markdown files
* Saving document edits
* Rendering previews

---

### Asset Controller

Handles:

* Image uploads
* Asset listing
* Asset deletion

---

# Chapter 6 – Markdown Rendering System

The Markdown rendering engine converts Markdown text into HTML.

### Rendering Process

1. Load Markdown file
2. Parse Markdown syntax
3. Generate HTML output
4. Render HTML in the browser

Supported features include:

* Headings
* Lists
* Images
* Links
* Code blocks

---

# Chapter 7 – Data Storage

MarkdownReader uses a **filesystem storage strategy**.

## 7.1 Markdown Documents

Documents are stored as `.md` files.

Example structure:

```
/public/markdown
   book1.md
   book2.md
```

---

## 7.2 Image Assets

Images used in Markdown files are stored in:

```
/public/markdown/images
```

Example Markdown image usage:

```
![Example](images/example.png)
```

---

# Chapter 8 – User Interface Design

The system includes two interfaces.

## 8.1 Reader Interface

The reader interface allows users to view Markdown books.

Features include:

* Clean layout
* Readable typography
* Responsive design

---

## 8.2 Admin Dashboard

The dashboard provides document management tools.

Features include:

* Markdown editor
* Document list
* Image asset manager
* Preview panel

---

# Chapter 9 – Sequence Diagrams

## Reading a Book

User → Router → Controller → Markdown File → Renderer → View

---

## Editing a Document

Admin → Editor → Controller → Save File → Storage

---

# Chapter 10 – Implementation Strategy

The implementation follows several principles.

### Modular Design

Each system component is separated into modules.

### Separation of Concerns

Controllers handle logic, while views handle presentation.

### File-Based Content

Markdown files eliminate the need for database complexity.

---

# Chapter 11 – Testing

Testing ensures the reliability of the system.

## Unit Testing

Individual modules are tested independently.

Examples:

* Markdown rendering
* File loading
* Asset uploads

---

## Integration Testing

Integration testing ensures that components work together correctly.

Examples:

* Editor → Preview system
* Controller → Renderer

---

# Chapter 12 – Security Considerations

The system implements several security measures.

### File Upload Validation

Image uploads are validated for:

* File type
* File size

---

### Input Sanitization

User input is sanitized to prevent malicious code execution.

---

# Chapter 13 – Performance Evaluation

MarkdownReader performs efficiently because:

* Files are loaded directly from disk.
* The rendering engine is lightweight.
* The system avoids database overhead.

These design decisions reduce latency and simplify deployment.

---

# Chapter 14 – Future Improvements

Several enhancements could improve the system.

### Full-Text Search

Allow searching within Markdown books.

### Syntax Highlighting

Support syntax highlighting for code blocks.

### Multi-user Support

Add authentication and role-based access control.

### Version Control Integration

Track document revisions using Git.

---

# Chapter 15 – Conclusion

MarkdownReader demonstrates that a lightweight Markdown management system can provide powerful functionality without the complexity of traditional content management systems.

By combining a web-based reader, Markdown editor, and asset management system, MarkdownReader provides a simple yet effective platform for managing technical documentation and digital books.

Future improvements could further enhance its usability and scalability, making it a strong foundation for Markdown-based publishing systems.

---

