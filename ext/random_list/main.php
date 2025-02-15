<?php

declare(strict_types=1);

namespace Shimmie2;

class RandomList extends Extension
{
    /** @var RandomListTheme */
    protected ?Themelet $theme;

    public function onPageRequest(PageRequestEvent $event)
    {
        global $config, $page;

        if ($event->page_matches("random")) {
            if (isset($_GET['search'])) {
                // implode(explode()) to resolve aliases and sanitise
                $search = url_escape(Tag::implode(Tag::explode($_GET['search'], false)));
                if (empty($search)) {
                    $page->set_mode(PageMode::REDIRECT);
                    $page->set_redirect(make_link("random"));
                } else {
                    $page->set_mode(PageMode::REDIRECT);
                    $page->set_redirect(make_link('random/'.$search));
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

            // set vars
            $images_per_page = $config->get_int("random_images_list_count", 12);
            $random_images = [];

            // generate random posts
            for ($i = 0; $i < $images_per_page; $i++) {
                $random_image = Image::by_random($search_terms);
                if (!$random_image) {
                    continue;
                }
                $random_images[] = $random_image;
            }

            $this->theme->set_page($search_terms);
            $this->theme->display_page($page, $random_images);
        }

        if ($event->page_matches("screenshots")) {
            if (isset($_GET['search'])) {
                // implode(explode()) to resolve aliases and sanitise
                $search = url_escape(Tag::implode(Tag::explode($_GET['search'], false)));
                if (empty($search)) {
                    $page->set_mode(PageMode::REDIRECT);
                    $page->set_redirect(make_link("screenshots/1"));
                } else {
                    $page->set_mode(PageMode::REDIRECT);
                    $page->set_redirect(make_link('screenshots/'.$search.'/1'));
                }
                return;
            }

            $search_terms = $event->get_search_terms();
            $search_terms_screenshot = $search_terms;
            array_push($search_terms_screenshot, "screenshot");

            $page_number = $event->get_page_number();
            $page_size = $event->get_page_size();

            $total_pages = Image::count_pages($search_terms_screenshot);
            $images = Image::find_images(($page_number-1)*$page_size, $page_size, $search_terms_screenshot);

            $count_images = count($images);


            if ($count_images === 1 && $page_number === 1) {
                $page->set_mode(PageMode::REDIRECT);
                $page->set_redirect(make_link('post/view/'.$images[0]->id));
            } else {
                $plbe = new PostListBuildingEvent($search_terms);
                send_event($plbe);

                $this->theme->set_page_screenshots($page_number, $total_pages, $search_terms);
                $this->theme->display_page_screenshots($page, $images);
                if (count($plbe->parts) > 0) {
                    $this->theme->display_admin_block($plbe->parts);
                }
            }
        }
    }

    public function onInitExt(InitExtEvent $event)
    {
        global $config;
        $config->set_default_int("random_images_list_count", 12);
    }

    public function onSetupBuilding(SetupBuildingEvent $event)
    {
        $sb = $event->panel->create_new_block("Random Posts List");

        // custom headers
        $sb->add_int_option(
            "random_images_list_count",
            "Amount of Random posts to display "
        );
    }

    public function onPageSubNavBuilding(PageSubNavBuildingEvent $event)
    {
        if ($event->parent=="posts") {
            $event->add_nav_link("posts_random", new Link('random'), "Shuffle");
        }
    }
}
