# Design Document
ie2 is built using the [CakePHP 2.x framework](https://cakephp.org/).  This provides us with a project skeleton that follows the typical MVC architecture.

ie2 is divided into several main components - called "Plugins".  The core plugin (which, actually, isn't a plugin) contains the bare minimum for user interaction.  It has the ability to have a dynamic index page, view injects, view attachments, and login.  In addition, the core plugin contains **no administrative interface** for typical CRUD operations.  Currently it only has the ability to Grade injects.

When you're adding a new feature, and unsure which plugin to put it into, ask yourself if it is tied to Injects.  If it is, then it should go to the core plugin.  If not, (including administrative interfaces), it should go into a separate plugin.

## Admin Plugin
The Admin Plugin provides an Administrative interface for basic CRUD operations on the core.

## BankWeb
The BankWeb Plugin is an integration with the [Bank-API](https://github.com/ubnetdef/bank-api) service.

## ScoreEngine
The ScoreEngine Plugin is an integration with the [Score Engine](https://github.com/ubnetdef/scoreengine).
