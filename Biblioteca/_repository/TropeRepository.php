
<?php

require_once __DIR__.'/../config/database.php';

class TropeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConexao();
    }

    public function listar(): array
    {
        $sql = "SELECT * FROM tropes ORDER BY trope";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarTropesLivro($livroId): array
    {
        $sql = "
        SELECT t.*
        FROM tropes t, livro_trope lt
        WHERE t.id_trope = lt.tropes_id and lt.livro_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id'=>$livroId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvarTropesLivro($livroId,array $tropes)
    {
        $stmt=$this->pdo->prepare(
        "INSERT INTO livro_trope(livro_id,tropes_id)
         VALUES(:livro,:trope)");

        foreach($tropes as $trope){

            $stmt->execute([
                ':livro'=>$livroId,
                ':trope'=>$trope
            ]);

        }

    }

    public function salvarTropesVinculadas(int $livroId, array $tropes){
        if(empty($tropes))
        return;

        $stmt=$this->pdo->prepare(
        "INSERT INTO livro_trope
        (livro_id,tropes_id)
        VALUES
        (:livro,:trope)");

        foreach($tropes as $trope){

         $stmt->execute([
            ':livro'=>$livroId,
            ':tropes'=>$trope
        ]);

    }

}
}