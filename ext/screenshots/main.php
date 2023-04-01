<?php

declare(strict_types=1);

namespace Shimmie2;

class ScreenshotList extends Extension
{
    /** @var ScreenshotListTheme */
    protected ?Themelet $theme;

    public function onPageRequest(PageRequestEvent $event)
    {
        global $config, $page;

        if ($event->page_matches("screenshots")) {
            if (isset($_GET['search'])) {
                // implode(explode()) to resolve aliases and sanitise
                $search = url_escape(Tag::implode(Tag::explode($_GET['search'], false)));
                if (empty($search)) {
                    $page->set_mode(PageMode::REDIRECT);
                    $page->set_redirect(make_link("screenshots"));
                } else {
                    $page->set_mode(PageMode::REDIRECT);
                    $page->set_redirect(make_link('screenshots/'.$search));
                }
                return;
            }

            if ($event->count_args() == 0) {
                $search_terms = [];
            } elseif ($event->count_args() == 1) {
                $search_terms = explode(' ', $event->get_arg(0));
            } else {
                throw new SCoreException("Error: too many arguments.");
            }

            array_push($search_terms, "screenshot");

            $page_number = $event->get_page_number();
            $page_size = $event->get_page_size();

            $total_pages = Image::count_pages($search_terms);
            $images = Image::find_images(($page_number-1)*$page_size, $page_size, $search_terms);

            $count_images = count($images);
            $count_search_terms = count($search_terms);


            if ($count_images === 1 && $page_number === 1) {
                $page->set_mode(PageMode::REDIRECT);
                $page->set_redirect(make_link('post/view/'.$images[0]->id));
            } else {
                $plbe = new PostListBuildingEvent($search_terms);
                send_event($plbe);

                $this->theme->set_page($page_number, $total_pages, $search_terms);
                $this->theme->display_page($page, $images);
                if (count($plbe->parts) > 0) {
                    $this->theme->display_admin_block($plbe->parts);
                }
            }
        }
    }


    public function onPageSubNavBuilding(PageSubNavBuildingEvent $event)
    {
        if ($event->parent=="posts") {
            $event->add_nav_link("posts_screenshots", new Link('screenshots'), "Screenshots");
        }
    }
}
