# Study Site Listing WordPress Plugin

A WordPress plugin for managing and displaying study site locations with an interactive map and search functionality.

## Recent Improvements

### Data Structure & Performance
- **Consolidated Data Storage**: Moved from multiple meta fields to a single `_location_data` meta field, reducing database queries
- **Optimized Location Queries**: Improved nearby location search with efficient distance calculations and caching
- **Geocoding Optimization**: Added caching and error handling for geocoding requests
- **Query Performance**: Reduced database load with better indexing and query structure

### Code Architecture
- **Modern PHP Practices**: 
  - Implemented proper namespacing
  - Added type hints and return types
  - Improved error handling with try-catch blocks
  - Better code organization with separate configuration class
- **Configuration Management**: 
  - Moved country and language data to dedicated configuration class
  - Dynamic loading of only used countries and languages
  - Better integration with WPML if available

### User Interface
- **Admin Interface**:
  - Redesigned location editor with a cleaner, more organized layout
  - Better form validation and error feedback
  - Improved meta box structure with logical grouping
  - Enhanced bulk edit capabilities
- **Frontend**:
  - Modern, responsive design
  - Better mobile compatibility
  - Improved map interaction
  - Enhanced search functionality

### Security & Validation
- **Input Validation**: Added proper sanitization for all user inputs
- **Nonce Verification**: Implemented for all forms and AJAX requests
- **Capability Checks**: Added proper user capability verification
- **API Key Security**: Better handling of Google Maps API key

### WordPress Integration
- **Best Practices**:
  - Proper hook usage
  - Better integration with WordPress core functions
  - Improved shortcode handling
  - Added REST API support
- **Asset Management**:
  - Conditional loading of scripts and styles
  - Better handling of dependencies
  - Support for minified versions in production

### Import/Export
- **Enhanced Import**:
  - Better CSV handling
  - Improved error reporting
  - Automatic geocoding during import
- **Export Functionality**:
  - Added comprehensive data export
  - Better CSV formatting
  - Included all location data fields

## Requirements
- WordPress 5.8 or higher
- PHP 8.0 or higher
- Google Maps API key

## Installation
1. Upload the plugin files to `/wp-content/plugins/study-site-listing`
2. Activate the plugin through the WordPress plugins screen
3. Go to Location Settings and enter your Google Maps API key
4. Use the shortcode `[store-locations map=yes]` to display the location finder

## Usage
### Shortcode Options
- `map`: Show/hide map (yes/no)
- `study`: Filter by study name

### Admin Features
- Add/Edit locations with full details
- Bulk import/export locations
- Manage languages and countries
- Customize map appearance
- Set default search radius

## Filters & Actions
The plugin provides various filters and actions for customization:
- `store_locations_distance_calculation`
- `store_locations_geocoding_result`
- `store_locations_search_query`
- `store_locations_map_options`

## Changelog
### 4.5.2
- Implemented consolidated data storage
- Added performance optimizations
- Improved UI/UX
- Enhanced security measures
- Added better WordPress integration
- Improved import/export functionality

## Credits
Originally developed for managing study site locations. Enhanced with modern WordPress development practices and optimizations.