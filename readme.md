# Rebase

Rebase is not a framework. It's a meta-framework and set of conventions for building large apps in Laravel. I try to codify how I name things and conventions I use below. I should also note that while this is open for anyone to use, it's really _my_ way of doing things, so I am probably not open to changes, but I'd be happy to talk about them with anyone who has a question. If you don't like this that's totally fine too, you don't need to use this, you don't need to like my standards either, I'm good with that.

## This is a WIP

If you're looking at this, cool, but there's a lot I've left out and I know it's not there at the moment. I've got a running list, please don't ask/tell me stuff, I know. :)

## First, PHP

This is using the latest version of PHP. While Laravel itself doesn't _need_ PHP 7.4, Rebase does make use of some 7.4 functionality so make sure you're on that locally, or you're gonna have a bad time. I'll update this to PHP 8 when it's stable, I usually wait about 2 to 4 weeks after release so if there are any gnarly bugs they're worked out.

## Directory Changes

There are no directory changes from a base Laravel standpoint. All folders are additive but more about organization, I did not and will not be changing the `app` folder. I get why some people think that's a good idea, but I'm personally against it. Why? Because it's just _one more thing_ I need to teach another developer. I've build some rather large Laravel projects the base structure is fine! When you move things, you now gotta remember where you put it and why, that's overhead you don't need.

### New Folders

All that being said, I did **add** folders, because adding folders makes perfect sense! Folders keep your code organized. So here are some new folders:

-  app

   -  Actions -- Actions are single action specific classes that do one thing See `GetView`
   -  Domain -- I think this makes sense, but I consider my domain to be my `models` and `repositories` that act on those models
      -  Models -- Eloquent models
      -  Repositories
         -  Facades -- Yes, I use facades for my `repositores` suck it, I love them.
   -  Enums
   -  Helpers -- A group of functions that help you in some way. See `DBWorkspace` for more understanding

-  database

   -  migrations
      -  shared -- these migrations run on the `shared` DB
      -  workspace -- these migrations run on the individual workspaces

-  stubs -- These are where I keep my stubs/overrides

## What this should and should not do

This isn't a full or complete application. This should do just the basics

## Shared vs Workspace

Because this is for setting up multi-tenant applications lots of of stuff in here need to be divided into either a landlord group or a tenant group. I never liked those terms personally. I guess I'm too literal of a person, but the idea of a "landlord" doesn't make sense to me. To me, the right terms are shared/workspace. So those terms are used everywhere. If you don't know what I'm talking about take a look at the `migrations` folder. In there you'll see a folder for `shared` and `workspace` those denote the two different databases that exist for each connection. The `shared` DB is the one that controls the account information and does the "routing" to the `workspace`.

So these terms are important to the system. They have meaning. Specifically, they are the division between user/account specific resources (`workspace`) and more global resources (`shared`). Try to keep that in mind while you're working on new system resources in your application and don't abuse workspace/shared names or the context will get confusing.

## Controller/View Names

While this does follow some basic RESTful conventions. This is not a RESTful API. These are URL's so we don't need to feel compelled to stick to a REST convention if/when it doesn't make sense, but generally these are the conventions that Laravel uses for Resource Controllers so these are the default names we start with:

| Method | URI | Name/Key | Controller Name | View Name |
| --- | --- | --- | --- | --- |
| GET | /photos | photos.index | PhotosIndex | PhotosIndex |
| GET | /photos/create | photos.create | PhotosCreate | PhotosCreate |
| POST | /photos/store | photos.store | PhotosStore | - |
| GET | /photos/{photo} | photos.show | PhotosShow | PhotosShow |
| GET | /photos/{photo}/edit | photos.edit | PhotosEdit | PhotosEdit |
| PATCH/PUT | /photos/{photo} | photos.update | PhotosUpdate | - |
| DELETE | /photos/{photo} | photos.destroy | PhotosDestroy | - |

### But these _are_ using restful conventions...so...wat?

Look the basics of RESTful naming make sense, I'm not saying they don't. So using the names is a great idea, _when they make sense to use._ Depending on the app you're building conventions you'll need to think through your own conventions, so don't become a REST-crazed nutter. Here are some names I use all the time that don't fit but work!

| Method | URI     | Name/Key            | Controller Name | View Name  |
| ------ | ------- | ------------------- | --------------- | ---------- |
| GET    | /login  | auth.login          | LoginIndex      | LoginIndex |
| POST   | /login  | auth.process.login  | ProcessLogin    | -          |
| POST   | /logout | auth.process.logout | ProcessLogout   | -          |

### `process` Controllers

There are some controllers that will never `store` or `update` the database in any way. They process data in some way, but that's about all they do. I think a great example of this is Login/Logout. You're not storing anything, you're just checking the data you're given. I guess you could name it something like `PostLogin` or `PostLogout` and `post.login` but why are we tying it to a method name? Just call-it-like-it-is: processing.

In case you're thinking, "login and logout are the only examples where this would make sense," I could make an argument for payments. Some people could make the argument that you're "updating the account" and that may very well be the case. But sometimes a payment is one-off, maybe the account doesn't get updated, or maybe your app does something where you need to FIRST process a payment THEN hand off control to something else before you update. Bottom line, you could have 1000 different things going on before an account update should ever happen. In that case, maybe `process` is the right name. All I'm saying is, this document doesn't prescribe anything in particular

## New Commands

Some of these commands are overrides of standard Laravel commands and some of these are new.

| Command | Description |
| --- | --- |
| `make:domain` | Generates the `nginx.conf` file for a custom domain and SSL certs |
| `make:model` | Generates a model |
| `make:controller` | Generates a controller |
| `make:view` | Generates an Inertia view file |
| `make:inertia-resource` | Generates a resource file |
| `make:repository` | Will generate a new repository and give you the code you need to add to the ServiceProvider |
| `db:migration` | Runs all the migrations either on shared, workspace or both |
| `db:explode` | _WILL ONLY RUN LOCALLY_ drops all Workspace DB's and refreshes the Shared DB |
| `db:rollback` | Rolls back the last migration either on shared, a single workspace, or all workspaces |
| `assets:compile` | Packages and compiles all the code |
| `assets:watch` | Runs a watcher over JS and Sass |
| `account:new` | A CLI way of creating a new workspace, good for spinning up tests and such |

## Routes

Currently, we only have the `web.php` file for handling routes. You should break this apart when it becomes necessary to do so. This depends on the application itself, but the guidance here is to be simple. In rebase, we're able to just go into the `RouteServiceProvider` and add another file name.

```php
    protected function map()
    {
        // add your file name to the list...
        $this->explicitRoute('workspace/web.php', 'web', 'admin', 'whatever');
    }
```

Again, you see we have the `workspace`/`shared` division here, but I've also added `public`. If you're wondering what a `public` route looks like think about registration, privacy pages, terms of service. `shared` routes are probably some super-admin level pages where you can view all accounts or listings too. You can add whatever files you'd like to the `routes` folder and it will be registered:

```php
    private function mapRoutes(?string ...$middleware): void
    {
        foreach (glob(base_path('routes/*.php')) as $file) {
            Route::middleware($middleware)
                 ->namespace($this->namespace)
                 ->group($file);
        }
    }
```

Take a look at that `RouteServiceProvider` file to see how we handle the middleware. This will probably change a bit in the future, I'm not 100% happy with this, but it works for now.

You don't _need_ folders, but logical/physical separation can help if you have a lot of routes...and I do mean _a lot_. Personally, if you have 20 routes files 5 lines of code in them I think you're doing it wrong. But that's me, I think looking at routes is a great way to get a sense of scope of an application so the more you can stuff into one file the better. Just be sensible.

## Sub Domain

Sub-domain routing ready to go out-of-the-box. I'll put some more stuff here soon.

## Custom Domains

These will work out-of-the-box too now. You'll need to make sure `certbot` is set up on your server so that can handle the whole process of cert setup and renewal. I'll put more info up here soon, but this all works for `nginx` only. I only use that so I have no plans on supporting something else

# Front End Resources

Rebase uses Vue and InertiaJS for the front end, not Blade. Since Inertia does work with React and other front ends, it is possible to use something else, if necessary, but the baseline components will be built using VueJS. This uses Laravel Mix because this is really just Laravel with some conventions spelled out, Laravel uses Mix by default so, so does Rebase.

## InertiaJS & VueJS

We use Inertia because the mix of Vue/Blade files drives me crazy. I want everything in one place and I don't want to build a freaking SPA. Why? Too many hassles in my opinion, but inertia is sensible. It gives you the power of Laravel/PHP for the backend and Vue (or whatever) for the front.

We still end up with a great separation of concerns because the controllers need to spit out data and the views handle it, so you don't end up doing anything stupid in the views.

## Rebase CSS

All CSS is styled via Sass. Check `app/resources/css` to see the folder structure. Styles are pre-processed with Sass and the resulting CSS is again processed with PostCSS. This allows us to use all the Sass-style goodness while also getting some interesting/useful plugins for optimizing our code via PostCSS. PostCSS is mostly used for optimization of code.

### Structure

The structure of the CSS has been optimized for sharing code between different applications while also allowing the app to make significant UI changes without affecting upstream systems (you still need to be careful when you pull in changes). Here's the structure:

```
   /css
    - shared
        - abstracts
            - functions
            - mixins
            - defaults
        - baseline
        - components
    - app
        - variables
        - components
```

The `shared` folder contains all the things that upstream may update at some point. Abstracts are filled with global `mixins` and `functions` to help make your sass life a little easier. `baseline` is an override of some things that happen in `normalize.css`. Nothing too serious, but just some general things that I like that would probably not be appropriate for `normalize`. The `shared/components` will be for global component mixins. These can be wholesale overridden by components in `app/components`. The idea is to just start with _something_ that looks decent enough.

The `app` folder is where application specific CSS will go. In there you'll find a `_variables.scss` file where you can add and override current default variables. All our current default values are located in `shared/abstracts/_defaults.scss`. It is recommended you _do not edit this file_ it's the shared systems default values. These can be overridden via a `git pull` so make no changes there. All your application changes should happen in the `app` folder. Every variable _can_ be overridden.

### Styles in `.vue` files or `.scss` files

Put them where you feel comfortable, but be consistent for the project.

## Potential Baseline Components

### Atoms

-  text
-  headlines
-  button
-  input (text & textarea)
-  input (select)
-  input (checkbox)
-  input (radio)
-  errors
-  label

### Molecules

-  dropdown
-  modal
-  accordion
-  popovers
-  toasts
-  tables (responsive)
-  cards
-  breadcrumbs
-  icons

### How to grid

Do not use flexbox for page-level layout. We use CSS Grids for the main aspects of all our pages. Flexbox should be used for things in **one** dimension--this means things like a navigation system, but not the container of the navigation system.

## Packages we use, but are not included

Running list of packages we don't include but have used and will use when the need comes up.

-  `yarn add @popperjs/core` -- popup blocks
-  `yarn add fuse.js` -- fuzzy search

# The rest is up to the project

At this point, this feels like the most we can create on a global level. There may be other things as we go along, but we're not going to try and force out everything, we're going to grow the system organically. I know, that can sometimes lead to terrible things happening, but, I'm a gardener...no seriously...one rule I've learned about gardening is that weeds happen, you can put down all the barriers and mulch you want, they will find a way. If you assume your barriers will _always_ work your garden will be overrun within a year. You've gotta prune it.

The same thing is true for a design system or meta-framework. Constant pruning is needed and it's never done. So that's my mentality here. We're not forcing conventions, but as they show up, we'll add them in
