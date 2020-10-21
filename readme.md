1. Clone project from GitHub
2. Composer install
3. php artisan serve
4. Check ".env" file to set up your DB connection (dbname, username, password)
5. Open home url
6. Fetch news with console command - php artisan parse:news

Implemented the following: 
1.Rendering on page the parsed Odessa news no older than 5 days
2.Publication date
3.Title of the article with a link to its page
4.Article author name
5.List of article tags separated by comma
6.Sorting by author name and date
