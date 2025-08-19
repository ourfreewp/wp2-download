# WP2 Architecture

This project is the **wp2-archi** component of the WP2 Download Manager. It provides a foundational toolkit for documenting and visualizing the architecture of a WordPress plugin or theme using Mermaid class diagrams. The core functionality is to collect architectural annotations from various parts of a codebase and generate a dynamic, interactive diagram.

## Features

- **Architecture Annotation Registry**  
	A centralized system to collect and manage component definitions from a codebase.

- **Mermaid Diagram Generation**  
	Converts structured component data into a visual, text-based Mermaid class diagram. The generator is highly configurable and supports a wide range of advanced features.

- **Caching**  
	The collected architectural data is cached using WordPress transients to prevent performance overhead on every page load. The cache can be manually flushed via a WP-CLI command.

- **Admin Page Integration**  
	Provides a dedicated page in the WordPress admin dashboard to view the generated architecture diagram.

- **REST API Endpoint**  
	Exposes a REST API endpoint to retrieve architectural data in either JSON or Mermaid format, allowing for external tool integration.

- **WP-CLI Integration**  
	Offers a set of commands for exporting the diagram to a file, flushing the cache, and rebuilding it.

## Architectural Components

The system revolves around a `Component` data structure, which is defined in the `Registry.php` file. A component is an array that can include the following keys:

- `component_id`: A unique identifier for the component.
- `namespace`: The logical grouping for the component.
- `type`: A general classification (e.g., Service, Manager, Database).
- `title`: A human-readable title.
- `facets`: An array of attributes or methods with their visibility (`+`, `-`, `#`, `~`), classifiers (`$`, `*`), parameters, return types, and support for generics.
- `relations`: An array defining connections to other components, including the type (`extends`, `depends`, `composition`, etc.), a label, and cardinality.
- `note`: A note to display next to the component in the diagram.
- `url`: A URL to make the component clickable in the diagram.

## Usage

### Viewing the Diagram in WordPress Admin

The easiest way to view the generated diagram is by navigating to the dedicated admin page. This page uses the `Generator` class to render the diagram directly in the browser.

### Using WP-CLI

The WP-CLI commands allow for headless, command-line usage.

- Export the diagram to a file:
	```sh
	wp wp2 archi export --file=./architecture.md
	```
- Export to STDOUT (for piping):
	```sh
	wp wp2 archi export > architecture.txt
	```
- Flush the cache:
	```sh
	wp wp2 archi flush-cache
	```
- Rebuild the cache:
	```sh
	wp wp2 archi rebuild-cache
	```

### Using the REST API

You can access the architectural data via a REST endpoint, which is useful for integrating with other services or front-end applications.

- **Endpoint:**  
	```
	[your-site]/wp-json/wp2/archi/v1/graph
	```

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