# Mai Display Taxonomy
Creates a "Display" taxonomy for use with Mai Theme's [grid] and other plugins.

## Example
1. Create a Display term called "Home Handpicked".
1. Select some posts and add them to Home Favorites term via the post edit screen or by bulk edit.
1. Use the following shortcode to display the selected posts on your homepage.
```
[grid content="post" taxonomy="mai_display" tax_field="slug" terms="home-handpicked"]
```

## Screenshots
**The Display taxonomy menu item and page.**<br>
![Display taxonomy menu](images/taxonomy-menu.png)<br>
**The Display taxonomy metabox (works with Classic Editor and Gutenberg block editor).**<br>
![Display taxonomy picker](images/taxonomy-picker.png)<br>
