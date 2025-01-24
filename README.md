# Interactive Module Generator

## About the Project

This project is a RESTful API that allows users to create and download reusable web components. These components are defined by properties like width, height, color, and clickable links. After defining a module, you can download it as a ZIP file containing HTML, CSS, and JavaScript files.

## API Endpoints

### 1. Create Module

**POST** `/api/modules`  
Defines a new module.  

**Request Body:**
- `type` (string): `background` or `typo`
- `width` (number): Width in %
- `height` (number): Height in %
- `color` (string): HEX color (for `background`)
- `link` (string): URL to open on click
- `content` (string): Text (for `typo`)

**Response:**
- `id` (number): ID of the created module.

### 2. Download Module

**GET** `/api/modules/{id}/download`  
Downloads a ZIP file with the module’s HTML, CSS, and JS files.

## Technologies Used

- **Laravel**: RESTful API backend
- **Docker**: For containerized development
- **HTML/CSS/JS**: To generate the components

## Contact

- **Email:** juliaaw.business@gmail.com  