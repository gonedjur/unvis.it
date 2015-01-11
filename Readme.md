# Unvis.it

[Unvis.it](http://unvis.it) is a tool to escape linkbaits, trolls, idiots and asshats.

What the tool does is to try to capture the content of an article or blog post without passing on your visit as a pageview. Effectively this means that you're not paying with your attention, so you can read and share the idiocy that it contains.

## Explanation

For Unvis.it's 2nd birthday, I've decided to open source it. This will make it blatantly obvious that I am in *no way* a developer, and that the fact that this works is merely a fluke. 

1. **There is no database** — because I find them hard to visualize and think about.
2. **This is basically a repackaged Instapaper/Pocket/ReadLater/whatever** — you could say that
3. **Horrible spaghetti code, awful comments! You're not a programmer** — yes, I know?
4. **This error/vulnerability/hack can cause this** — please help me fix it!


## The flow

A user will paste a link, or type unvis.it before the url in the address bar and then the script goes to work:

- CURL will fetch the URL with a randomized webcrawler user agent, in order to not stand out in server logs.
- The web page will then be parsed through PHP Readability (by Fivefilters.org) to remove ads and other stuff.
- If the base64 sum of the url is NOT found as a .txt-file in the cache-folder, it will save it with that name and serve it.
- If the base64 sum of the url IS found as a .txt-file in the cache folder: serve that file instead.
- Now the user can share the link to the contents without providing traffic to stuff the user doesn't want to support.

## TODO
Things I would like to do, but have very little knowledge in how to achieve. If anyone feels that they can and want to contribute, I would appreciate it a lot.

* Use memcache to speed things up
* Use MySQL instead of files
* Consolidate URL-caching so that www. and trailing slashes don't create separate caches
* Rehost images on imgur
* Log debug output when Readability cannot find the main text
* Something else you thought of, that I wouldn't in a million years...

## Built with:
- PHP-Readability 1.7.2	: https://github.com/j0k3r/php-readability
- SimpleCache		: http://devgrow.com/simple-cache-class/
- SmartOptimizer	: https://github.com/farhadi/SmartOptimizer
- Bootstrap 2.2.2	: https://github.com/twbs/bootstrap/releases/tag/v2.2.2
- GAPI				: http://code.google.com/p/gapi-google-analytics-php-interface/

## Plugins 
- Firefox : https://addons.mozilla.org/en-US/firefox/addon/unvisit/
- Chrome  : https://github.com/mindstormer/unvisit_chromeextension 
