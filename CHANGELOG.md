## 1.1.2

- Metadata and metaboxes
- Parent/Child helpers

## 1.1.1

- Split out starting object validation into a Validator class
- Split the feed out of the custom post type to stand on its own, as it's a separate feature and functionality.
- README.md instructions

## 1.1.0
- Giving themes love too by adding a Theme Addon module.  Now you pass the initialization and setup
configuration from the theme to Fulcrum and it handles the setup for you.

## 1.0.5
- Environment functionality wrapped in now with `FULCRUM_ENV` and `fulcrum_is_dev_environment`
- Changed `plugin.php` to `bootstrap.php` to clarify that it is the bootstrapping file.
- Added database helper support functions for hard option queries.

## 1.0.4
- Fixed a Doh moment does_post_name_exist_for_same_term_id() to use the db prefix for term_relationships table.

## 1.0.3
- `fulcrum_add_button_class` and `fulcrum_add_grid_to_post_class` added to `support/helpers.php`
- Taxonomy template added to template manager.

## 1.0.2

Plugin activation rewrite handlers for the Addon class.

## 1.0.1

Registering back-end (admin) service providers. There's no need to load these when not in the admin dashboard.

## 1.0.0

Initial release.