<?php

declare(strict_types=1);

namespace Shimmie2;

class RandomListTheme extends Themelet
{
    protected array $search_terms;

    /**
     * #param string[] $search_terms
     */

    public function set_page(array $search_terms)
    {
        $this->search_terms = $search_terms;
    }

    public function set_page_screenshots(int $page_number, int $total_pages, array $search_terms)
    {
        $this->page_number = $page_number;
        $this->total_pages = $total_pages;
        $this->search_terms = $search_terms;
    }

    /**
     * #param Image[] $images
     */
    public function display_page(Page $page, array $images)
    {
        $page->title = "Random Posts";

        $html = "<b>Refresh the page to view more posts</b>";
        if (count($images)) {
            $html .= "<div class='shm-image-list'>";

            foreach ($images as $image) {
                $html .= $this->build_thumb_html($image);
            }

            $html .= "</div>";
        } else {
            $html .= "<br/><br/>No posts were found to match the search criteria";
        }

        $page->add_block(new Block("Random Posts", $html));

        $nav = $this->build_navigation($this->search_terms);
        $page->add_block(new Block("Navigation", $nav, "left", 0));
    }

    public function display_page_screenshots(Page $page, array $images)
    {
        $page->title = "Screenshots";

        if (count($images)) {
            $html .= "<div class='shm-image-list'>";

            foreach ($images as $image) {
                $html .= $this->build_thumb_html($image);
            }

            $html .= "</div>";
        } else {
            $html .= "<br/><br/>No posts were found to match the search criteria";
        }

        $page->add_block(new Block("Screenshots", $html));

        $nav = $this->build_navigation_screenshots($this->page_number, $this->total_pages, $this->search_terms);
        $page->add_block(new Block("Navigation", $nav, "left", 0));

        if (count($this->search_terms) > 0) {
            if ($this->page_number > 3) {
                // only index the first pages of each term
                $page->add_html_header('<meta name="robots" content="noindex, nofollow">');
            }
            $query = url_escape(Tag::caret(Tag::implode($this->search_terms)));
            $this->display_paginator($page, "screenshots/$query", null, $this->page_number, $this->total_pages, true);
        } else {
            $this->display_paginator($page, "screenshots", null, $this->page_number, $this->total_pages, true);
        }
    }


    /**
     * #param string[] $search_terms
     */
    protected function build_navigation_screenshots(int $page_number, int $total_pages, array $search_terms): string
    {
        $prev = $page_number - 1;
        $next = $page_number + 1;

        $u_tags = url_escape(Tag::implode($search_terms));
        $query = empty($u_tags) ? "" : '/'.$u_tags;


        $h_prev = ($page_number <= 1) ? "Prev" : '<a href="'.make_link('screenshots'.$query.'/'.$prev).'">Prev</a>';
        $h_index = "<a href='".make_link()."'>Index</a>";
        $h_next = ($page_number >= $total_pages) ? "Next" : '<a href="'.make_link('screenshots'.$query.'/'.$next).'">Next</a>';

        $search_terms_fixed = array_diff($search_terms, ["screenshot"]);
        $h_search_string = html_escape(Tag::implode($search_terms_fixed));
        $h_search_link = make_link();
        $h_search = "
			<p><form action='$h_search_link' method='GET'>
				<input type='search' name='search' value='$h_search_string' placeholder='Search' class='autocomplete_tags' autocomplete='off' />
				<input type='hidden' name='q' value='/screenshots'>
				<input type='submit' value='Find' style='display: none;' />
			</form>
		";

        return $h_prev.' | '.$h_index.' | '.$h_next.'<br>'.$h_search;
    }


    protected function build_navigation(array $search_terms): string
    {
        $h_search_string = html_escape(Tag::implode($search_terms));
        $h_search_link = make_link("random");
        $h_search = "
			<p><form action='$h_search_link' method='GET'>
				<input type='search' name='search' value='$h_search_string' placeholder='Search random list' class='autocomplete_tags' autocomplete='off' />
				<input type='hidden' name='q' value='/random'>
				<input type='submit' value='Find' style='display: none;' />
			</form>
		";

        return $h_search;
    }
}
