# Post Voting Plugin

A simple Wordpress post voting plugin with positive and negative votes using Ajax.

## Installation

Download [release](https://github.com/vedranbe/simplevote/releases/tag/Wordpress) and unpack "simplevote" folder to */wp-content/plugins*. After that you just have to activate it. 


## Preview

![Screenshot 2024-01-15 182546](https://github.com/vedranbe/simplevote/assets/8826823/42b88181-76a6-40f0-84dc-9845c4800682)

## Usage

Usage is as simple as it gets. You have only two options to choose from in the "Was this article helpful?" section. Right below the post.

This plugin does not use cookies. It checks whether you are logged in or not. If not, it will check your IP address. You can only vote once.

It uses AJAX. When you choose what to vote for, it disables voting buttons, but shows what you voted for. Other fields are automatically changed according to data from database and your input.

If you want to test how it works with multiple votes, you'll need to use your WP database. You just need to go to your **wp_usermeta table** and find the **meta_key** called **voted_posts**. Just delete it and you can do it again.

In case you want to clear all votes for any post, just go to **wp_postmeta table** and delete any **meta_key** that contains **positive** or **negative**. That's where all the votes count.

There are two fields "Thank you for your feedback.". One counts positive, other negative votes as primary. There was no explanation in the task, so it was left like this.

## Other

Everything is tested on WordPress 6.4.2.

All pointer actions are disabled after voting. Styling is as close as it can be. It is also mobile friendly. There are only 3 files, PHP, JS and CSS. All functions inside files are commented.

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

