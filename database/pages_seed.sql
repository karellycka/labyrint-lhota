-- =====================================================
-- SEED DATA - Homepage
-- =====================================================
-- Vytvoření homepage s editovatelnými texty
-- =====================================================

INSERT INTO `pages` (`slug`, `template`, `published`) VALUES
('home', 'home', 1);

-- České překlady
INSERT INTO `page_translations` (`page_id`, `language`, `title`, `meta_description`, `content`) VALUES
(1, 'cs', 'Škola Labyrint', 'Škola Labyrint - Montessori pedagogika od roku 2016. Připravujeme děti na reálný život.',
'<h2>Vracíme do školy smysluplnost</h2>
<h3>Učení je život a život je učení.</h3>

<h4>Jsme malá škola, velká duchem</h4>
<p>Tvoříme školu, kde se dobře učí – a dobře žije. Je nás dohromady přes 120 – od 1. do 9. ročníku. Místo autoritářství a hierarchie pěstujeme vztahy, osobní důvěru a lidskost.</p>

<h4>10+ let děláme Montessori pedagogiku</h4>
<p>Vedeme děti k tomu, aby samy věděly, co dělají a proč – a převzaly za to odpovědnost. Montessori není jen o dřevěných pomůckách a slovním hodnocení. Je to vnitřní postoj plný respektu, jasných hranic a smysluplné práce.</p>

<h4>Naši absolventi jdou příkladem</h4>
<p>Absolventi Labyrintu mají vědomosti i charakter. Jsou samostatné, odolné, ukotvené a otevřené. Své schopnosti dokáží využít ve prospěch svůj, ale i pro dobro společnosti.</p>

<h4>Pracovitost a smysluplnost máme v krvi</h4>
<p>Klademe důraz na samostatnou práci vlastním tempem i spolupráci napříč školou. Pěstujeme hard i soft skills, máme hodně angličtiny a milujeme vše, co můžeme rovnou převést do praxe.</p>

<h2>Opravdovost spolu s odpovědností</h2>
<h3>Propojujeme znalosti, dovednosti a hodnoty. Už od roku 2016.</h3>

<h4>Přípravná třída: „Labyrintíci"</h4>
<h5>Přirozenost, praktičnost a hra</h5>
<p>Děti do učení zapojují všechny smysly, pobyt venku a praktické činnosti, které jim přináší radost ze samostatnosti.</p>

<h4>1.–6. ročník: „Mravenečníci" a „Keprníci"</h4>
<h5>Vlastní tempo, kritické myšlení, věkově smíšené třídy</h5>
<p>Učíme děti samostatně přemýšlet a spolupracovat napříč věkem. Díky respektujícímu vedení, smysluplným činnostem a Montessori pomůckám objevují vnitřní zápal pro učení.</p>

<h4>7.–9. ročník: „Študáci"</h4>
<h5>Teorie v praxi, samostudium, osobní rozvoj</h5>
<p>Děti berou odpovědnost do vlastních rukou – samostatně studují, zkoumají, kým jsou, diskutují o společnosti a tvoří projekty, které je posouvají k osobnostní i ekonomické nezávislosti.</p>');

-- Anglické překlady
INSERT INTO `page_translations` (`page_id`, `language`, `title`, `meta_description`, `content`) VALUES
(1, 'en', 'Labyrinth School', 'Labyrinth School - Montessori pedagogy since 2016. Preparing children for real life.',
'<h2>Bringing meaning back to school</h2>
<h3>Learning is life and life is learning.</h3>

<h4>We are a small school, big in spirit</h4>
<p>We create a school where learning is good – and living is good. There are over 120 of us – from 1st to 9th grade. Instead of authoritarianism and hierarchy, we cultivate relationships, personal trust and humanity.</p>

<h4>10+ years of Montessori pedagogy</h4>
<p>We guide children to know what they are doing and why – and take responsibility for it. Montessori is not just about wooden materials and verbal assessment. It is an inner attitude full of respect, clear boundaries and meaningful work.</p>

<h4>Our graduates lead by example</h4>
<p>Labyrinth graduates have knowledge and character. They are independent, resilient, grounded and open. They can use their abilities for their own benefit, but also for the good of society.</p>

<h4>Hard work and meaningfulness are in our DNA</h4>
<p>We emphasize independent work at their own pace and collaboration across the school. We cultivate both hard and soft skills, we have a lot of English and we love everything we can put into practice right away.</p>

<h2>Authenticity along with responsibility</h2>
<h3>We connect knowledge, skills and values. Since 2016.</h3>

<h4>Prep class: "Labyrintíci"</h4>
<h5>Naturalness, practicality and play</h5>
<p>Children engage all their senses in learning, outdoor time and practical activities that bring them joy in independence.</p>

<h4>Grades 1-6: "Mravenečníci" and "Keprníci"</h4>
<h5>Own pace, critical thinking, mixed-age classes</h5>
<p>We teach children to think independently and collaborate across ages. Through respectful guidance, meaningful activities and Montessori materials, they discover an inner passion for learning.</p>

<h4>Grades 7-9: "Študáci"</h4>
<h5>Theory in practice, self-study, personal development</h5>
<p>Children take responsibility into their own hands – they study independently, explore who they are, discuss society and create projects that move them towards personal and economic independence.</p>');
