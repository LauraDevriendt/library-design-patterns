<?php
abstract class PagesOverview
{
    abstract function getSearchInput():string;
    abstract function searchMatch(Library $library): array;

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
    /**
     * @var Book[]
     */
    private array $books;
    public function __construct(string $searchInput)
    {
        $this->searchInput = strtolower($searchInput);
    }

    public function getSearchInput(): string
    {
        return $this->searchInput;
    }

     public function addBook(Book $book)
    {
        $this->books[] = $book;
    }

    public function searchMatch(Library $library): array
    {
        $searchMatches = [];
        foreach ($library->getBooks() as $book) {
            if (stripos(strtolower($book->getTitle()), strtolower($this->searchInput) )!== false) {
                $searchMatches[] = $book;
            }
        }
        return $searchMatches;

    }
}

class Genre extends PagesOverview
{
    /** @var Book[] */
    private $books = [];
    private string $genre;

    public function __construct(string $genre)
    {
        $this->genre = strtolower($genre);
    }

    public function addBook(Book $book)
    {
        $this->books[] = $book;
    }

    public function getSearchInput():string
    {
        return $this->genre;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function searchMatch(Library $library): array
    {

        $searchMatches = [];
        foreach ($library->getBooks() as $book) {

            if (stripos(strtolower($book->getGenre()->getGenre()), strtolower($this->getSearchInput()) )!== false) {
                $searchMatches[] = $book;
            }
        }
        return $searchMatches;
    }
}

class Publisher extends PagesOverview
{
    /** @var Book[] */
    private $books = [];
    private string $publisher;

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function addBook(Book $book)
    {
        $this->books[] = $book;
    }

    public function __construct(string $publisher)
    {
        $this->publisher = strtolower($publisher);
    }

    public function getSearchInput():string
    {
        return $this->publisher;
    }

    public function searchMatch(Library $library): array
    {
        $searchMatches = [];
        foreach ($library->getBooks() as $book) {
            if (stripos(strtolower($book->getPublisher()->getPublisher()), strtolower($this->getSearchInput()) )!== false) {
                $searchMatches[] = $book;
            }
        }
        return $searchMatches;
    }
}

