# ProTorque site: Kinsta deployment

Everything needed is in this folder:

| File | What it is |
|---|---|
| `protorque-theme-0.3.0.zip` | The custom theme. All page layouts, fonts, images, hero video and the approved page copy are inside it. |
| `protorque-content.xml` | WordPress content export: 40 pages, 27 news posts, the main menu. Imported once through Tools > Import. |

The theme is self-contained. It does not need WPBakery, The7, or any of the dev-site plugins.

## 1. Create the site in Kinsta

1. MyKinsta > Sites > Add site > **Install WordPress**.
2. Site name: ProTorque. Data centre: closest to Calgary (Toronto or Montréal). Language: English.
3. Set the admin username, password and email (shane@machinemedia.ca for now; transfer later).
4. Leave WooCommerce and Yoast unticked here. Yoast is installed in step 3.
5. When it finishes, open **WordPress admin** from the site's Info tab (the temporary address looks like `protorque.kinsta.cloud`).

## 2. Theme

1. Appearance > Themes > **Add New Theme** > **Upload Theme** > choose `protorque-theme-0.3.0.zip` > Install Now > **Activate**.
2. Appearance > Themes: delete the default Twenty Twenty-* themes (optional, keeps things tidy).

On activation the theme sets permalinks to `/%postname%/`, prepares the Careers job post type, and creates the two Contact Form 7 forms as soon as that plugin is active.

## 3. Plugins

Plugins > Add New, search and install each, then Activate:

| Plugin | Why |
|---|---|
| **Contact Form 7** | The Request a Quote form (Contact page) and the newsletter form (News page). The theme creates both forms automatically. |
| **Yoast SEO** | Titles, meta descriptions, sitemap, schema. |
| **GTM Kit** | Google Tag Manager container `GTM-WJJMKJV` is already in the theme header, so GTM Kit is optional. Install it only if you want tag settings in wp-admin; if you do, leave its container ID blank or remove the theme snippet so the container is not loaded twice. |
| **Safe SVG** | Lets SVG logos be uploaded to the media library. |

Kinsta's own cache plugin (Kinsta MU) is already there. No page builder, no TablePress (spec tables are built into the theme), no The7.

## 4. Content import

1. Tools > Import > WordPress > **Install Now**, then **Run Importer**.
2. Choose `protorque-content.xml` > **Upload file and import**.
3. Assign posts to the admin user you created in step 1 (or let it create "Paul"/"Machine Media" authors; either is fine).
4. Leave **Download and import file attachments** unticked. Images are not in the XML; the theme attaches the news featured images itself the next time wp-admin loads after the import.
5. Submit. It takes under a minute.

After the import, open any wp-admin page once (the Dashboard is fine). The theme then:

- assigns the **Main Menu** to the primary navigation,
- sets **Home** as the front page and **News** as the posts page,
- attaches the featured image to each of the 27 news posts,
- publishes the 5 job postings on Careers (the dev-site test postings; replace them under **Careers** in the admin menu).

Check: Appearance > Menus shows Main Menu in the "Primary navigation" location; Settings > Reading shows Home / News; Settings > Permalinks shows Post name. If any of those is not set, save that screen once.

## 5. Contact forms

Contact > Contact Forms should list **Main Contact Form** and **Newsletter Signup**. Both send to the site admin email with Reply-To set to the sender. To change recipients, edit the form's Mail tab. Kinsta sends mail through its own transactional service, so no SMTP plugin is required; test a submission from the Contact page.

## 6. Settings to confirm

- Settings > General: Site Title "ProTorque", Tagline empty, Timezone Edmonton, email address.
- Settings > Reading: Search engine visibility **ticked** until launch (so the kinsta.cloud address is not indexed). Untick at launch.
- Settings > Discussion: untick "Allow people to submit comments on new posts".
- Yoast: run the configuration wizard, set the organisation name and logo, and confirm the sitemap at `/sitemap_index.xml`.

## 7. Launch (when the client signs off)

1. Add the domain `ptenergy.com` in MyKinsta > Domains, make it primary; Kinsta runs the search-and-replace from the kinsta.cloud address.
2. Point DNS at Kinsta per the instructions shown there (A record / CNAME), then issue the free SSL.
3. Settings > Reading: untick Search engine visibility.
4. Set up 301 redirects from the old site's URLs (MyKinsta > Redirects). The news posts keep their old slugs, but the old site served them under `/portfolio/…`; one regex rule covers them: `^/portfolio/(.*)$` → `/$1`.
5. Submit the sitemap in Google Search Console and check GA4 is receiving data through the GTM container.

## What is still placeholder or pending (not blockers for review)

- Homepage statistics (0 TRIR, kilometres, miles, man hours) are the dev-site figures; client to confirm.
- Social links in the footer point to the network home pages until the real profile URLs are supplied.
- Footer mark is a placeholder PNG until Sam supplies the white mark.
- Charcoal texture, map and red gradient are theme-built stand-ins for Sam's assets.
- Maison Neue web licence to confirm.
- Privacy Policy exists as a draft with WordPress's default text; client to supply.
- Flush Mounted Spider: the second spec table ("Maximum torque at 2000 psi…") carries the same row labels as the first table on both the dev site and ptenergy.com; the client should confirm the intended values.
- News featured images for the five newest posts (Aug–Oct 2026) use client photos from the dev-site library instead of the exact images on ptenergy.com (those files were not in the export); swap them in the media library if preferred.
- The five Careers postings are the dev-site test entries (Power Tong Operator 1–5).

## Updating later

Page copy for the landing, service and equipment pages lives in the theme (`inc/content/pages.json`), not in the WordPress editor, so a copy change is a theme update: edit, re-zip, Appearance > Themes > Upload Theme > "Replace current with uploaded". News posts, jobs, forms and the menu are editable in wp-admin as normal. Source is at https://github.com/MachineMediaX/protorque-theme.
