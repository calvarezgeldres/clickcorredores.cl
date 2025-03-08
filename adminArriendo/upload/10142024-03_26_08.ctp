<h1>Lista de Usuarios</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?php echo h($usuario->id); ?></td>
            <td><?php echo h($usuario->nombre); ?></td>
            <td><?php echo h($usuario->email); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
