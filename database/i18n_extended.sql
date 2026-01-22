-- Extended i18n translations for homepage and common elements
-- Run this after schema.sql or merge with it

INSERT INTO `translations` (`key_name`, `language`, `value`, `context`) VALUES

-- Homepage
('homepage.welcome', 'cs', 'Vítejte ve škole Labyrint', 'homepage'),
('homepage.welcome', 'en', 'Welcome to Labyrint School', 'homepage'),
('homepage.subtitle', 'cs', 'Moderní škola s inovativním přístupem k výuce', 'homepage'),
('homepage.subtitle', 'en', 'Modern school with innovative approach to education', 'homepage'),
('homepage.learn_more', 'cs', 'Dozvědět se více', 'homepage'),
('homepage.learn_more', 'en', 'Learn More', 'homepage'),
('homepage.contact_us', 'cs', 'Kontaktujte nás', 'homepage'),
('homepage.contact_us', 'en', 'Contact Us', 'homepage'),
('homepage.recent_posts', 'cs', 'Nejnovější články', 'homepage'),
('homepage.recent_posts', 'en', 'Latest Posts', 'homepage'),
('homepage.view_all_posts', 'cs', 'Zobrazit všechny články', 'homepage'),
('homepage.view_all_posts', 'en', 'View All Posts', 'homepage'),
('homepage.upcoming_events', 'cs', 'Nadcházející události', 'homepage'),
('homepage.upcoming_events', 'en', 'Upcoming Events', 'homepage'),
('homepage.view_all_events', 'cs', 'Zobrazit všechny události', 'homepage'),
('homepage.view_all_events', 'en', 'View All Events', 'homepage'),
('homepage.cta_title', 'cs', 'Máte zájem o naši školu?', 'homepage'),
('homepage.cta_title', 'en', 'Interested in our school?', 'homepage'),
('homepage.cta_description', 'cs', 'Kontaktujte nás pro více informací nebo se přijďte podívat na den otevřených dveří', 'homepage'),
('homepage.cta_description', 'en', 'Contact us for more information or visit us on our open day', 'homepage'),
('homepage.cta_button', 'cs', 'Kontaktovat školu', 'homepage'),
('homepage.cta_button', 'en', 'Contact School', 'homepage'),

-- Footer
('footer.description', 'cs', 'Škola Labyrint - Moderní vzdělávání pro 21. století', 'footer'),
('footer.description', 'en', 'Labyrint School - Modern education for the 21st century', 'footer'),
('footer.quick_links', 'cs', 'Rychlé odkazy', 'footer'),
('footer.quick_links', 'en', 'Quick Links', 'footer'),
('footer.contact', 'cs', 'Kontakt', 'footer'),
('footer.contact', 'en', 'Contact', 'footer'),
('footer.rights', 'cs', 'Všechna práva vyhrazena', 'footer'),
('footer.rights', 'en', 'All rights reserved', 'footer'),

-- Blog
('blog.read_time', 'cs', ':minutes min čtení', 'blog'),
('blog.read_time', 'en', ':minutes min read', 'blog'),
('blog.share', 'cs', 'Sdílet', 'blog'),
('blog.share', 'en', 'Share', 'blog'),
('blog.related_posts', 'cs', 'Související články', 'blog'),
('blog.related_posts', 'en', 'Related Posts', 'blog'),
('blog.categories', 'cs', 'Kategorie', 'blog'),
('blog.categories', 'en', 'Categories', 'blog'),
('blog.no_posts', 'cs', 'Zatím nejsou k dispozici žádné články', 'blog'),
('blog.no_posts', 'en', 'No posts available yet', 'blog'),

-- Events
('events.upcoming', 'cs', 'Nadcházející události', 'events'),
('events.upcoming', 'en', 'Upcoming Events', 'events'),
('events.past', 'cs', 'Minulé události', 'events'),
('events.past', 'en', 'Past Events', 'events'),
('events.no_events', 'cs', 'Žádné nadcházející události', 'events'),
('events.no_events', 'en', 'No upcoming events', 'events'),
('events.location', 'cs', 'Místo', 'events'),
('events.location', 'en', 'Location', 'events'),
('events.time', 'cs', 'Čas', 'events'),
('events.time', 'en', 'Time', 'events'),
('events.add_to_calendar', 'cs', 'Přidat do kalendáře', 'events'),
('events.add_to_calendar', 'en', 'Add to Calendar', 'events'),

-- Error messages
('error.404_title', 'cs', 'Stránka nenalezena', 'errors'),
('error.404_title', 'en', 'Page Not Found', 'errors'),
('error.404_message', 'cs', 'Omlouváme se, ale hledaná stránka neexistuje', 'errors'),
('error.404_message', 'en', 'Sorry, the page you are looking for does not exist', 'errors'),
('error.500_title', 'cs', 'Chyba serveru', 'errors'),
('error.500_title', 'en', 'Server Error', 'errors'),
('error.500_message', 'cs', 'Omlouváme se, něco se pokazilo. Zkuste to prosím později', 'errors'),
('error.500_message', 'en', 'Sorry, something went wrong. Please try again later', 'errors'),
('error.back_home', 'cs', 'Zpět na úvodní stránku', 'errors'),
('error.back_home', 'en', 'Back to Homepage', 'errors');
