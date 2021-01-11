# Sneezing Trees Beaver Builder Extension

A WordPress plugin that creates content modules. The modules can be used both with Beaver Builder drag and drop and with Advanced Custom Fields style editing.

The plugin allows theme developers to create a set of modules that adhere to a consistent design while taking full advantage of the Beaver Builder and ACF interfaces for easy editing by users.

**NB Work in Progress!**

The plugin isn't finished. It works, but no doubt there are bugs in it, and there are more modules to come. You are welcome to use it and develop it for your own purposes though.

Please note also that this plugin integrates with both Beaver Builder and Advanced Custom Fields. Both need to be activated for it to function.

# How to use

## Beaver Builder mode

Once the plugin is activated, when you edit a page or post using Beaver Builder you will find that the standard Beaver Builder modules are no longer available and have been replaced by others.

Working with these modules is essentially identical to working with the standard Beaver Builder modules. Some key differences are:

- These modules are intended for use in a theme where page and post templates are set to full width. Each module renders a `<section>` that ideally should take up the full width of the site / screen. Every module comes with the option to add a background image and/or a colour with variable opacity. The colour element sits over the background image so it can be used as a colour filter over the image.

- The plugin enqueues the [Bootstrap grid system](https://getbootstrap.com/docs/4.0/layout/grid/), and the content in each module then sits within a `div` with a class of `container`. This means that while the background image and colour is full width, the content is constrained by the bootstrap grid width. Content in the modules then uses the bootstrap column classes to take advantage of the grid layout.

- The ability to create columns using Beaver Builder functionality has been disabled, as well as the ability to create individual elements such as buttons and headings. The basic building block is instead the full-width `<section>` which acts as a row.

## ACF mode

The Beaver Builder mode allows site users a great deal of flexibility - in essence putting content modules wherever they want.

The ACF mode allows site administrators to constrain users a bit more. For example, the site design may demand that the first section of a particular page should always contain content in a particular format - a big quote, say, or a heading followed by a sub-heading, or an image to the right of some text. Or perhaps several pages / posts need the same format of content, but located at different fixed places on the page / post. Or maybe for at time a banner is needed on all posts i.e., a module that uses not only the same content format, but also the same content.

Typically all this would be achieved through a template and ACF, but any design changes then have to be made to the template itself. This plugin allows the design to be made dynamically.

The plugin creates two new items in the Admin Menu - **Content Modules** and **Fixed Content Editors**.

### *Variable* content modules

When a content module is created, the essential choice is what type of module it is. Most of the plugin's Beaver Builder style of modules are available to choose from. Then you can choose where the content appears, on which post types, and for pages you can select individual pages.

Content is then either selected as *Variable* or *Fixed*.

If variable is chosen, then the same content fields as you would see in the Beaver Builder version appear (but in ACF format) whenever a page / post is being edited - provided that page / post has been selected to include that content. Thus the content can be different on each page / post where the module is present.

### *Fixed* content modules

If fixed is chosen, then a new Fixed Content Editor is created. This again contains exactly the same fields as you would see in the Beaver Builder version of the module, but in ACF format. And this time the content can only be edited here in a single place, so the content is then identical where it appears throughout the site.

These content modules can either be hooked to `the_content` so they appear in the main content section of the page / post, or they can be hooked to any of the theme actions. This makes it simple to create variable or fixed banners, header heroes, footer elements, etc.

# Example

Take a look at the [first site](https://sandbox.sneezingtrees.com/charlieswines/) where I am using the plugin. The site, like the plugin, is still work in progress, so expect rough and ready in areas!