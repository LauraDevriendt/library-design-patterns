<?php
abstract class PagesOverview
{
    abstract function getSearchInput():string;
    abstract function SearchMatch(Library $library, $searchInput): array;

    public function getPages(array $searchMatches): array
    {
        $pages = [];
        foreach ($searchMatches as $match) {
            $pages[] = $match->getBookPages();
        }
        return $pages;
    }

    public function totalPages(PagesOverview $searchInput, array $searchMatches): int
    {
        $filteredMatches=[];
     foreach ($searchMatches as $match){
        if($match->getContext()->getState()->isVisible()){
           $filteredMatches[]=$match;
        }
     }

        $totalPages = 0;
        foreach ($searchInput->getPages($filteredMatches) as $page) {
            $totalPages += $page;
        }
        return $totalPages;
    }


}

class PartialBookSearch extends PagesOverview
{
    private string $searchInput;

    public function __construct(string $searchInput)
    {
        $this->searchInput = strtolower($searchInput);
    }

    public function getSearchInput(): string
    {
        return $this->searchInput;
    }

    public function SearchMatch(Library $library, $searchInput): array
    {
        $searchMatches = [];
        foreach ($library->getBooks() as $book) {
            if (stripos(strtolower($book->getTitle()), strtolower($searchInput) )!== false) {
                $searchMatches[] = $book;
            }
        }
        return $searchMatches;

    }
}

class Genre extends PagesOverview
{
    private string $genre;

    public function __construct(string $genre)
    {
        $this->genre = strtolower($genre);
    }

    public function getSearchInput():string
    {
        return $this->genre;
    }

    public static function getGenres(Library $library)
    {
        $genres = [];
        foreach ($library->getBooks() as $book) {
            $genres[] = $book->getGenre();
        }
        $genres = array_unique($genres);
        return $genres;
    }
    public function SearchMatch(Library $library, $searchInput): array
    {
        $searchMatches = [];
        foreach ($library->getBooks() as $book) {
            if (stripos(strtolower($book->getGenre()), strtolower($searchInput) )!== false) {
                $searchMatches[] = $book;
            }
        }
        return $searchMatches;

    }
}

class Publisher extends PagesOverview
{
    private string $publisher;


    public function __construct(string $publisher)
    {
        $this->publisher = strtolower($publisher);
    }

    public function getSearchInput():string
    {
        return $this->publisher;
    }

    public static function getPublishers(Library $library)
    {
        $publishers = [];
        foreach ($library->getBooks() as $book) {
            $publishers[] = $book->getPublisher();
        }
        $publishers = array_unique($publishers);
        return $publishers;
    }
    public function SearchMatch(Library $library, $searchInput): array
    {
        $searchMatches = [];
        foreach ($library->getBooks() as $book) {
            if (stripos(strtolower($book->getPublisher()), strtolower($searchInput) )!== false) {
                $searchMatches[] = $book;
            }
        }
        return $searchMatches;

    }
}

