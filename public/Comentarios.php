<div class="mt-4">
    <h3>Comentarios</h3>
    <div class="card-comment-container">
        <?php
        $sql_comments = "CALL GetProductComments($productID)";
        $result_comments = $conn->query($sql_comments);
        while ($comment = $result_comments->fetch_assoc()) : ?>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h5 class="funky-text col-md-7"><?php echo htmlspecialchars($comment['Usuario']); ?></h5>
                        <h5 class=" col-md-5 text-right">Fecha: <?php echo htmlspecialchars($comment['Fecha']); ?> Rating: <?php echo htmlspecialchars($comment['Rating']); ?></h5>
                    </div>
                    <p class="card-text-producto"><?php echo htmlspecialchars($comment['Texto']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
