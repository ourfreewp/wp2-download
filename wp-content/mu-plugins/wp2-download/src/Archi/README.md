# WP2 Architecture

This project is the **wp2-archi** component of the WP2 Download Manager. It provides a foundational toolkit for documenting and visualizing the architecture of a WordPress plugin or theme using Mermaid class diagrams. The core functionality is to collect architectural annotations from various parts of a codebase and generate a dynamic, interactive diagram.

## Features

- **Architecture Annotation Registry**: A centralized system to collect and manage component definitions from a codebase.
- **Mermaid Diagram Generation**: Converts structured component data into a visual, text-based Mermaid class diagram. This diagram can then be rendered in a browser, a code editor, or any tool that supports Mermaid.
- **Caching**: The collected architectural data is cached using WordPress transients to prevent performance overhead on every page load. The cache can be manually flushed via a WP-CLI command.
- **Admin Page Integration**: Provides a dedicated page in the WordPress admin dashboard to view the generated architecture diagram.
- **REST API Endpoint**: Exposes a REST API endpoint to retrieve architectural data in either JSON or Mermaid format, allowing for external tool integration.
- **WP-CLI Integration**: Offers a set of commands for exporting the diagram to a file, flushing the cache, and rebuilding it.

## Architectural Components

The system revolves around a **Component** data structure, which is defined in the `Registry.php` file. A component is an array that can include the following keys:

- `component_id`: A unique identifier for the component.
- `namespace`: The logical grouping for the component.
- `type`: A general classification (e.g., Service, Manager, Database).
- `title`: A human-readable title.
- `description`: A brief description of the component.
- `facets`: An array of attributes or methods with their visibility (public, private, protected), classifiers (static, abstract), parameters, and return types.
- `relations`: An array defining connections to other components, including the type (extends, depends, composition, etc.), a label, and cardinality.
- `note`: A note to display next to the component in the diagram.
- `url`: A URL to make the component clickable in the diagram.

## Usage

### Viewing the Diagram in WordPress Admin

The easiest way to view the generated diagram is by navigating to the dedicated admin page. This page uses the `Generator` class to render the diagram directly in the browser.

### Using WP-CLI

The WP-CLI commands allow for headless, command-line usage.

Export the diagram to a file:

```sh
wp wp2 archi export --file=./architecture.md
```

Export to STDOUT (for piping):

```sh
wp wp2 archi export > architecture.txt
```

Flush the cache:

```sh
wp wp2 archi flush-cache
```

Rebuild the cache:

```sh
wp wp2 archi rebuild-cache
```

### Using the REST API

You can access the architectural data via a REST endpoint, which is useful for integrating with other services or front-end applications.

- **Endpoint:** `[your-site]/wp-json/wp2/archi/v1/graph`
- **Parameters:**
	- `facet`: Filter components by a specific facet (e.g., `?facet=core`)
	- `format`: `json` or `mermaid` (default: `json`)

## Code Structure

- `Init.php`: The main entry point for the plugin, responsible for bootstrapping the core components.
- `Registry.php`: The central data store for all architectural annotations, handling data collection, normalization, and caching.
- `Caching.php`: Manages the transient cache for the architectural data.
- `Helpers.php`: Provides a simple `register_annotation` helper function to add components to the registry.
- `Admin/Page.php`: Defines the WordPress admin page and renders the Mermaid diagram.
- `Viz/Mermaid/Generator.php`: The core logic for converting component data into a valid Mermaid class diagram string. This class handles syntax generation, escaping, and formatting.
- `REST/Controller.php`: Implements the REST API endpoint for accessing the architectural data.
- `CLI/Commands.php`: Contains the WP-CLI commands for interacting with the architecture registry.

## Contributing

For information on how to add your own architectural annotations, please refer to the internal documentation and examples provided.

## Usage Guide

This guide explains how to define and register your architectural components so they can be visualized by the wp2-archi system.

### 1. Registering Components

The core of the system is the Registry, which collects component data from your codebase. You can register a component manually using the `register_annotation()` helper function. This function is designed to be called from a file within your plugin or theme, typically in a file that defines the class or component itself.

Each component is an array that defines its properties and relationships.

#### Basic Registration Example

Here is a simple example of how to register a component, in this case a REST API controller.

```php
<?php
// my-plugin/includes/REST/Controller.php

use WP2\Download\Archi\Helpers;

// Define your component data.
$rest_controller = [
		'component_id' => 'rest_controller',
		'namespace'    => 'REST',
		'type'         => 'Controller',
		'title'        => 'REST API Controller',
		'facets'       => [
				[ 'name' => 'permissions_check', 'visibility' => 'public', 'returnType' => 'bool' ],
				[ 'name' => 'register_routes', 'visibility' => 'public' ],
				[ 'name' => 'get_graph', 'visibility' => 'public' ],
		],
		'relations'    => [
				[ 'to' => 'registry', 'type' => 'depends', 'label' => 'fetches data from' ]
		]
];

// Register the component with the system.
Helpers\register_annotation( $rest_controller );
```

The `register_annotation()` function uses a WordPress filter to add your component to the central Registry. You can call this helper function as many times as you need to define all of your components.

### Defining Relations

Relationships are defined within the `relations` array. Each relation specifies the target component's `component_id` and the type of the relationship.

| Relation Type | Description                       | Example |
|---------------|-----------------------------------|---------|
| depends       | A general dependency or association| `-->`   |
| extends       | A class inherits from another     | `<|--`  |
| composition   | A component is composed of another| `*--`   |
| aggregation   | A component contains another      | `o--`   |
| realization   | A component implements an interface| `..|>`  |
| lollipop      | A component implements a public interface | `--()` |

### Defining Facets

The `facets` array is used to define the public, private, and protected members of your class, including methods and properties.

- `name`: The name of the member.
- `visibility`: The member's scope (`public`, `private`, `protected`, or `package`).
- `parameters`: An array of parameters for a method.
- `returnType`: The method's return type.
- `classifier`: The member's type (`static` or `abstract`).

### 2. Generate a Component Prompt

To help you get started with the manual process of defining components, you can use the following prompt. It is designed to analyze a PHP code file and generate a starter component definition based on the class it finds.

**Prompt:**

> Analyze the following PHP code file and generate a component definition array for the class it contains. The array should follow the structure documented in the wp2-archi system, including:
> - A `component_id` that is the lowercase version of the class name.
> - A `namespace` based on the file path.
> - A `type` based on the class's purpose (e.g., 'Controller', 'Service', 'Manager').
> - A `title` that is the human-readable class name.
> - A `facets` array containing all public, private, and protected methods and properties.
> - A `relations` array if the class uses or extends other components.
>
> Here is the code file:

