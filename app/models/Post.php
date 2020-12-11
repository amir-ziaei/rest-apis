<?php
/**
 * Post Model
 */

namespace App\Model;

class Post extends Model
{
    protected static $table = "posts";

    protected $id;
    public $title;
    public $body;
    public $slug;
    public $author;
    public $categories;
    protected $created_at;
    protected $updated_at;

    protected $guarded = ["categories"];

    public static function allWithFilters(string $search, string $order = "desc", int $year = 0, string $cats = "")
    {
        $instance = new static;
        $sql = "SELECT posts.*,
                       MATCH (title, body, slug) AGAINST (:search) AS relevance,
                       MATCH (title) AGAINST (:search) AS title_relevance
                FROM posts";
        if($cats) {
            $sql .= " JOIN posts_categories
                    ON posts_categories.post_id = posts.id
                    WHERE posts_categories.category_id IN (SELECT ID FROM categories WHERE FIND_IN_SET (title, :cats))";
            if($search || $year)
                $sql .= " AND";
        } else if($search || $year)
            $sql .= " WHERE";
        if(!empty($search)) {
            $sql .= " MATCH (title, body, slug) AGAINST (:search)";
            if($year)
                $sql .= " AND";
        }
        if($year)
                $sql .= " YEAR(created_at) = ${year}";
        if($search)
            $sql .= " ORDER BY title_relevance ${order}, relevance ${order}";
        else
            $sql .= " ORDER BY created_at ${order}";

        $instance->db->query($sql);

        $search = "'${search}' @4";
        $instance->db->bind(":search", $search);

        if($cats)
            $instance->db->bind(":cats", $cats);

        return $instance->db->fetchAll(get_called_class());
    }

    public function getCategories()
    {
        $sql = "select cat.*
                from categories cat
                inner join posts_categories pc on cat.id = pc.category_id
                inner join posts p on pc.post_id = p.id
                where p.id = :post_id";
        $this->db->query($sql);
        $this->db->bind(":post_id", $this->id);
        return $this->db->fetchAll(Category::class);
    }

    public function setCategories($id, $rows)
    {

        $sql = "DELETE FROM posts_categories WHERE post_id =:id";
        $this->db->query($sql);
        $this->db->bind(":id", $id);
        $this->db->execute();

        $row_length = count($rows[0]);
        $nb_rows = count($rows);
        $length = $nb_rows * $row_length;

        $args = implode(',', array_map(
            function($el) { return '('.implode(',', $el).')'; },
            array_chunk(array_fill(0, $length, '?'), $row_length)
        ));

        $params = array();
        foreach($rows as $row)
        {
            foreach($row as $value)
            {
                $params[] = $value;
            }
        }

        $sql = "INSERT IGNORE INTO posts_categories(post_id, category_id)
                VALUES ".$args;
        $this->db->query($sql);
        $this->db->execute($params);
    }

}
