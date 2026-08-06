# Welcome to the Block Party!
My personal code junk drawer. Here for anyone to rifle through. While this is a WordPress plugin, it is not a finished product. Keeping things a in a plugin is a useful container for working on and testing code that's ultimately destined for other plugins, themes, libraries, codebases, etc.

Please don't use this plugin outside a self-contained, local development environment.

Current status: I'm using a pause between gigs to update and add some new stuff. Stuff that's coming soon is noted below.

## Publication Date
This little block of questionable utility stems from a client request made before the post date block existed. (It was introduced into core with version 5.9 and full-site editing.) In the right context, it still has its uses.

## Document Preview
The core file block is a great little tool for site authors to disseminate media items. Its display of an embedded PDF preview in supporting browsers is a great feature. A client had one further addition they wanted to see: similar preview embedding of the main Microsoft Office document formats. Watch out, there's gray beard trickery at work here. I was lucky enough to remember a trick for generating previews of office documents.

Or, you know, just use PDFs...

## Article Preview
Fetch open graph tags for a given URL and, if found, use them to create a preview card. Kinda like what happens in slack. Cards have two layouts. Sidebar fields allow for local overrides of fetched, or incomplete/blocked, data. 

There is necessary backend code for this block that creates a custom endpoint for fetching open graph data by communicating back to the server (which can then cache it!) rather than trying to make those requests from the client. Uses the embed package from packagist.org, with help from guzzle, to do the fetching and parsing of tags. Alas, this little guy fails checks for bots that many sites use. On the todo list: find ways to improve reliability in fetching.

## Meta Powers! (still to come)
This Boilerplate/starter code for Document Settings panel slotfills with some common patterns for manipulating registered meta values in the sidebar. One possible way of bringing old-school metaboxes more fully into the block editor. Lots of plugins already do this, so this is my started code for adding similar functionality.

This grew out of request to not use ACF! What you see here is absolutely not a complete replacement but could grow into one with enough effort. Currently it handles text values, media library selection, and post selection via a custom component.

## Where in the world is... (still to come)
A basic map block, but without the big G! Yes, it's possible, darlings, to make a map without Google. This block leverages the venerable leaflet.js open source library and OpenStreetMap tiles as a privacy-friendly, more ethical alternative.