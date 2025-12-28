<table>
    <thead>
        <th>Имя</th>
        <th>Год рождения</th>
    </thead>
    <tbody>
        <?php foreach ($readers as $reader): ?>
            <tr>
                <td><?= $reader["name"] ?></td>
                <td><?= $reader["year"] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>