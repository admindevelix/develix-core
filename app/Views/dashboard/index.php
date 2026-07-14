<main>

    <div class="container-fluid">

        <h2 class="mb-4">Dashboard</h2>

        <div class="row g-4">

            <?php
                $label = 'Vendas Hoje';
                $value = 'R$ 0,00';
                $this->component('stat-card', compact('label', 'value'));

                $label = 'Pedidos';
                $value = '0';
                $this->component('stat-card', compact('label', 'value'));

                $label = 'Produtos';
                $value = '0';
                $this->component('stat-card', compact('label', 'value'));

                $label = 'Downloads';
                $value = '0';
                $this->component('stat-card', compact('label', 'value'));
            ?>

        </div>

    </div>

</main>