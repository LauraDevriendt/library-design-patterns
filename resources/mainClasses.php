<?php

class Library
{
    protected array $books = [];
    protected array $genres = [];
    protected array $publishers = [];


    public function __construct(array $books, array $genres, array $publishers)
    {
        $this->books = $books;
        $this->genres = $genres;
        $this->publishers = $publishers;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getPublishers(): array
    {
        return $this->publishers;
    }


    public function getBooks(): array
    {
        return $this->books;
    }

    public function searchBook(string $title): Book
    {

        foreach ($this->getBooks() as $book) {
            if ($title === $book->getTitle()) {
                return $book;
            }
        }
    }

    public function displayBooks(array $searchMatches): string
    {
        $completeDisplay = "";
        foreach ($searchMatches as $match) {
            $status = get_class($match->getContext()->getState());
            $title = urlencode($match->getTitle());
            $display = "<div class='card mr-2 mb-2 col-3'>
                      <h2>Book</h2>
                      <p><strong>Title: </strong>{$match->getTitle()}</p>
                      <p><strong>Genre: </strong>{$match->getGenre()->getGenre()}</p>
                      <p><strong>Publisher: </strong>{$match->getPublisher()->getPublisher()}</p>
                       <p><strong>Status: </strong>{$status}</p>";


            foreach ($match->getContext()->getState()->validTransactions() as $item) {
                switch ($item) {
                    case LendedState::class:
                        $display .= "<a href='?title=$title&state=lended'>Borrow</a>";
                        break;
                    case OpenState::class:
                        $display .= "<a href='?title=$title&state=open'>Return</a>";
                        break;
                    case SoldState::class:
                        $display .= "<a href='?title=$title&state=sold'>Buy</a>";
                        break;
                    case LostState::class:
                        $display .= "<a href='?title=$title&state=lost'>Lost it</a>";
                        break;
                }
            }

           /* switch ($status) {
                case 'LendedState':
                    $display .= "
                      <a href='?title=$title&state=lost'>I Lost it</a>
                      <a href='?title=$title&state=open'>Return Book</a>
                      </div>";
                    break;
                case 'OpenState':
                    $display .= "
                      <a href='?title=$title&state=lended'>Borrow</a>
                      <a href='?title=$title&state=sold'>buy it</a>
                      </div>";
                    break;
                case 'LostState' || 'SoldState':
                    $display = "";
                    break;
            }*/
            $completeDisplay .= $display."</div>";

        }
        return $completeDisplay;
    }



}

class Book
{
    private string $title;
    private string $author;
    private Genre $genre;
    private int $pages;
    private Publisher $publisher;
    private Context $context;


    public function __construct(string $title, string $author, Genre $genre, int $pages, Publisher $publisher)
    {
        $this->title = $title;
        $this->author = $author;
        $this->genre = $genre;
        $this->pages = $pages;
        $this->publisher = $publisher;
        $this->context = new Context(new OpenState());


        $this->genre->addBook($this);
        $this->publisher->addBook($this);

    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGenre(): Genre
    {
        return $this->genre;
    }

    public function getBookPages(): int
    {
        return $this->pages;
    }

    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

}
