-- =====================================================
-- HOMEPAGE WIDGETS MIGRATION
-- =====================================================
-- Migrace všech 8 widgetů z hardcoded home.php do databáze
-- Page ID 1 = homepage (slug='home')
-- =====================================================

-- Widget 1: HERO SECTION
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'hero', 0, 1);
SET @w1 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w1, 'cs', '{"title":"Připravujeme děti na","rotatingText":[{"text":"reálný život"},{"text":"střední školu"},{"text":"zodpovědnou dospělost"},{"text":"celoživotní zápal pro učení"}],"ctaButtons":[{"text":"Zjistit více","url":"#about","variant":"primary"}],"overlay":true,"height":"full"}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w1, 'en', '{"title":"We prepare children for","rotatingText":[{"text":"real life"},{"text":"high school"},{"text":"responsible adulthood"},{"text":"lifelong passion for learning"}],"ctaButtons":[{"text":"Learn more","url":"#about","variant":"primary"}],"overlay":true,"height":"full"}');

-- Widget 2: ASYMMETRIC TEXT + VIDEO
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'asymmetric_text_video', 1, 1);
SET @w2 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w2, 'cs', '{"text":"<h2>Tvoříme bezpečný a podporující prostor, kde děti mohou rozvíjet svou přirozenou zvídavost a touhu učit se.</h2>","textBackgroundColor":"var(--color-primary)","videoId":"SOGz3Vz9aNs","aspectRatio":"16:9"}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w2, 'en', '{"text":"<h2>We create a safe and supportive environment where children can develop their natural curiosity and desire to learn.</h2>","textBackgroundColor":"var(--color-primary)","videoId":"SOGz3Vz9aNs","aspectRatio":"16:9"}');

-- Widget 3: FEATURE CARDS GRID 2x2
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'feature_cards_grid', 2, 1);
SET @w3 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w3, 'cs', '{"sectionTitle":"Vracíme do školy smysluplnost","sectionSubtitle":"Učení je život a život je učení.","cards":[{"title":"Jsme malá škola, velká duchem","text":"<p>Tvoříme školu, kde se dobře učí – a dobře žije. Je nás dohromady přes 120 – od 1. do 9. ročníku. Místo autoritářství a hierarchie pěstujeme vztahy, osobní důvěru a lidskost.</p>","backgroundColor":"var(--color-brown)"},{"title":"10+ let děláme Montessori pedagogiku","text":"<p>Vedeme děti k tomu, aby samy věděly, co dělají a proč – a převzaly za to odpovědnost. Montessori není jen o dřevěných pomůckách a slovním hodnocení. Je to vnitřní postoj plný respektu, jasných hranic a smysluplné práce.</p>","backgroundColor":"var(--color-yellow)"},{"title":"Naši absolventi jdou příkladem","text":"<p>Absolventi Labyrintu mají vědomosti i charakter. Jsou samostatné, odolné, ukotvené a otevřené. Své schopnosti dokáží využít ve prospěch svůj, ale i pro dobro společnosti.</p>","backgroundColor":"var(--color-blue)"},{"title":"Pracovitost a smysluplnost máme v krvi","text":"<p>Klademe důraz na samostatnou práci vlastním tempem i spolupráci napříč školou. Pěstujeme hard i soft skills, máme hodně angličtiny a milujeme vše, co můžeme rovnou převést do praxe.</p>","backgroundColor":"var(--color-red)"}]}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w3, 'en', '{"sectionTitle":"We bring meaning back to school","sectionSubtitle":"Learning is life and life is learning.","cards":[{"title":"We are a small school, big in spirit","text":"<p>We create a school where people learn well - and live well.</p>","backgroundColor":"var(--color-brown)"},{"title":"10+ years of Montessori pedagogy","text":"<p>We guide children to know what they are doing and why.</p>","backgroundColor":"var(--color-yellow)"},{"title":"Our graduates lead by example","text":"<p>Labyrinth graduates have both knowledge and character.</p>","backgroundColor":"var(--color-blue)"},{"title":"Hard work and purpose are in our blood","text":"<p>We emphasize independent work at your own pace.</p>","backgroundColor":"var(--color-red)"}]}');

-- Widget 4: BLOG POSTS GRID
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'blog_posts_grid', 3, 1);
SET @w4 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w4, 'cs', '{"sectionTitle":"Novinky","limit":3,"showExcerpt":true,"showAuthor":true,"showDate":true,"viewAllButton":true,"viewAllButtonText":"Všechny články"}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w4, 'en', '{"sectionTitle":"News","limit":3,"showExcerpt":true,"showAuthor":true,"showDate":true,"viewAllButton":true,"viewAllButtonText":"All articles"}');

-- Widget 5: QUOTE #1
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'quote', 4, 1);
SET @w5 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w5, 'cs', '{"quoteId":1,"text":"","author":"","role":""}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w5, 'en', '{"quoteId":1,"text":"","author":"","role":""}');

-- Widget 6: FEATURE CARDS WITH IMAGE 1x3
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'feature_cards_with_image', 5, 1);
SET @w6 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w6, 'cs', '{"sectionTitle":"Opravdovost spolu s odpovědností","sectionSubtitle":"Propojujeme znalosti, dovednosti a hodnoty. Už od roku 2016.","cards":[{"image":"/public/assets/images/skola/DSC_7507.jpg","title":"Přípravná třída: Labyrintíci","subtitle":"Přirozenost, praktičnost a hra","text":"<p>Děti do učení zapojují všechny smysly, pobyt venku a praktické činnosti.</p>","backgroundColor":"var(--color-brown)"},{"image":"/public/assets/images/skola/DSC_7397.jpg","title":"1.–6. ročník: Mravenečníci a Keprníci","subtitle":"Vlastní tempo, kritické myšlení","text":"<p>Učíme děti samostatně přemýšlet a spolupracovat napříč věkem.</p>","backgroundColor":"var(--color-yellow)"},{"image":"/public/assets/images/skola/DSC_7587.jpg","title":"7.–9. ročník: Študáci","subtitle":"Teorie v praxi, samostudium","text":"<p>Děti berou odpovědnost do vlastních rukou.</p>","backgroundColor":"var(--color-blue)"}]}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w6, 'en', '{"sectionTitle":"Authenticity with responsibility","sectionSubtitle":"We connect knowledge, skills and values.","cards":[{"image":"/public/assets/images/skola/DSC_7507.jpg","title":"Preparatory class","subtitle":"Naturalness and play","text":"<p>Children engage all their senses in learning.</p>","backgroundColor":"var(--color-brown)"},{"image":"/public/assets/images/skola/DSC_7397.jpg","title":"Grades 1-6","subtitle":"Own pace, critical thinking","text":"<p>We teach children to think independently.</p>","backgroundColor":"var(--color-yellow)"},{"image":"/public/assets/images/skola/DSC_7587.jpg","title":"Grades 7-9","subtitle":"Theory in practice","text":"<p>Children take responsibility.</p>","backgroundColor":"var(--color-blue)"}]}');

-- Widget 7: CTA IMAGE BANNER
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'cta_image_banner', 6, 1);
SET @w7 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w7, 'cs', '{"backgroundImage":"/public/assets/images/hero/slide-3.jpg","title":"Poznejte naši školu","subtitle":"Přijďte se vzdělávat, nahlédnout do výuky nebo s námi slavit.","buttonText":"Navštivte nás","buttonUrl":"/kontakt","buttonVariant":"primary","overlay":true}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w7, 'en', '{"backgroundImage":"/public/assets/images/hero/slide-3.jpg","title":"Get to know our school","subtitle":"Come to learn, see the teaching or celebrate with us.","buttonText":"Visit us","buttonUrl":"/kontakt","buttonVariant":"primary","overlay":true}');

-- Widget 8: QUOTE #2
INSERT INTO page_widgets (page_id, widget_type_key, display_order, is_active) VALUES (1, 'quote', 7, 1);
SET @w8 = LAST_INSERT_ID();
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w8, 'cs', '{"quoteId":null,"text":"Labyrint mě naučil, že zvládnu cokoliv, pokud budu chtít.","author":"Honza","role":"absolvent"}');
INSERT INTO page_widget_translations (page_widget_id, language, settings) VALUES (@w8, 'en', '{"quoteId":null,"text":"Labyrinth taught me that I can handle anything if I want to.","author":"Honza","role":"graduate"}');
