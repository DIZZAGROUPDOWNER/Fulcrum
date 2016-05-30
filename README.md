# Fulcrum Plugin

Fulcrum - The customization central repository to extend and custom WordPress. This plugin provides the centralized infrastructure for the custom plugins and theme.

### Customization Central
This plugin provides a central location for all redundant functionality.  It keeps your plugins and theme DRY, reusable, and modular.  It is meant to be extended.  Therefore, when you need a feature-specific plugin like a Portfolio, Testimonials, or FAQ, you extend the `Fulcrum\Addon\Addon` class in your plugin.  Then you configure what service providers you need.

It saves you a ton of time and code in your plugins.

### Features
This plugin is fully crafted in OOP.  It utilizes [DI Container](http://pimple.sensiolabs.org/), Dependency Injection, Polymorphism, Inheritance, etc.  It shows you how to build OOP-capable plugins.

It also uses:
* Composer and its autoload functionality in place of filling a function with includes and requires.
* Gulp as it's task runner
* Config files, which abstract the runtime configuration out of the modules and into `fulcrum/config` folder where they belong.
* Service Providers for the Addons to utilize, which simply the need-to-know in the addons.  Configure and fire them up.
 
Includes:
* [Pimple](http://pimple.sensiolabs.org/) - as the DI Container
* Shortcodes     
* Meta boxes     
* Custom Post Types  
* Custom Taxonomy    
* Widgets

### Some Cool Packages
Fulcrum includes some cool packages to make your job more fun.
* [Kint](http://raveren.github.io/kint/) - a modern and powerful PHP debugging helper
* [Whoops](http://filp.github.io/whoops/) - PHP Errors for Cook Kids
* [Carbon](http://carbon.nesbot.com/) - A simple PHP API extension for DateTime.
* [Pimple](http://pimple.sensiolabs.org/) - as the DI Container

## Installation

Installation from GitHub is as simple as cloning the repo onto your local machine.  Typically, I put Fulcrum as a must use plugin.  Why? Because the child theme and all custom plugins extend off of it.  Therefore, you want it to always be activated.

To install it as a must use, here's what you want to do:

1. Open your project and navigate to `wp-content/mu-plugins`.
2. Then type: `git clone https://github.com/hellfromtonya/Fulcrum.git`.
3. Next you need an autolauncher to load Fulcrum.  If one exists already, then add `include( 'fulcrum/bootstrap.php' );` into it.  Otherwise, do the following:
* Navigate to `fulcrum/mu-loader/` and copy the file `mu_autoloader.php`
* Paste it into the root of `wp-content/mu-plugins`.
* Bam, Fulcrum now loads itself up without you or your client needing to activate it.  WooHoo!

## Contributions

All feedback, bug reports, and pull requests are welcome.