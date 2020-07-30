<?php

class BooksNotImportedException extends Exception {}



class BookImporterCsv
{
    private string $path;
    /** @var Book[]  */
    private array $books = [];
    /** @var Genre[]  */
    private array $genres = [];
    /** @var Publisher[]  */
    private array $publishers = [];

    public function __construct(string $path)
    {
        $this->path = $path;

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
            if(!isset($this->publishers[$book['publisher']])) {
                $this->publishers[$book['publisher']] = new Publisher($book['publisher']);
            }

            $this->books[] = new Book(
                $book['title'],
                $book['author'],
                $this->genres[$book['genre']],
                (int)$book['pages'],
                $this->publishers[$book['publisher']]
            );
        }

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

    /**
     * @return Publisher[]
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }
}

class BookImporterJson
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
            $this->books[] = new Book($book['title'], $book['author'], new Genre($book['genre']), (int)$book['pages'], new Publisher($book['publisher']));
        }

        return $this->books;
    }


}
