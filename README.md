# Mai Display Taxonomy
This plugin creates a new taxonomy that is hidden on the front end to create custom "Display" term locations for use with [grid].

## Example:
1. Create a Display term called "Home Handpicked".
1. Select some posts and add them to Home Favorites term via the post edit screen or by bulk edit.
1. Use the following shortcode to display the selected posts on your homepage.
```
[grid content="post" taxonomy="mai_display" tax_field="slug" terms="home-handpicked"]
```
![Display taxonomy menu](images/taxonomy-menu.png)<br>
![Display taxonomy picker](images/taxonomy-picker.png)
