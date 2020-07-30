<?php

class BooksNotImportedException extends Exception {}

abstract class BookImporter
{
    const FORMAT = "JSON"; // @TODO NODIG???

    abstract public function convertData(): array;

    public function getData(): array
    {
        // Call the factory method to create a Product object...
        return $this->convertData();


    }


}

class BookImporterCsv extends BookImporter
{
    private string $path;
    /** @var Book[]  */
    private array $books = [];
    /** @var Genre[]  */
    private array $genres = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function convertData(): array //merge it with constructor
    {
        $bookData = [];
        if (($handle = fopen($this->path, "r")) === FALSE) {
            throw new BooksNotImportedException('Could not read books');
        }

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (!isset($first)) {
                $first = false;
                continue;
            }

            [$title, $author, $genre, $pages, $publisher] = $data;
            $bookData[] = [
                'title' => $title,
                'author' => $author,
                'genre' => $genre,
                'pages' => $pages,
                'publisher' => $publisher,
            ];
        }
        fclose($handle);
        foreach ($bookData as $book) {
            if(!isset($this->genres[$book['genre']])) {
                $this->genres[$book['genre']] = new Genre($book['genre']);
            }

            $this->books[] = new Book(
                $book['title'],
                $book['author'],
                $this->genres[$book['genre']],
                (int)$book['pages'],
                $book['publisher'],
                new OpenState()
            );
        }
        return $this->books;
    }

    /**
     * @return Genre[]
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @return Book[]
     */
    public function getBooks(): array
    {
        return $this->books;
    }
}

class BookImporterJson extends BookImporter
{
    private string $path;
    private array $books = [];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function convertData(): array
    {
        if(!is_file($this->path)) {
            throw new BooksNotImportedException('Could not read json books');
        }

        $bookData = json_decode(file_get_contents($this->path, true), true, 512, JSON_THROW_ON_ERROR);
        foreach ($bookData as $book) {
            $this->books[] = new Book($book['title'], $book['author'], $book['genre'], (int)$book['pages'], $book['publisher'], new OpenState());
        }

        return $this->books;
    }


}

function clientCode(BookImporter $creator): array
{

    return $creator->getData();

}