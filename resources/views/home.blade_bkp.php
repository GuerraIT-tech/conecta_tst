@extends('layouts.app')

@section('title', 'HCL Licitações - Inteligência e Eficiência Escalável')

@section('content')

    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <div class="logo">conectar</div>
            <ul class="nav-menu">
                <li><a href="#planos">Planos</a></li>
                <li><a href="#ferramentas">Ferramentas</a></li>
                <li><a href="#conectar">O Conectar</a></li>
                <li><a href="#ajuda">Ajuda</a></li>
            </ul>
            <div class="nav-actions">
                <a href="admin/login" class="btn-login">Acessar Conta</a>
                <a href="admin/register" class="btn-register">Cadastrar</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Plataforma de licitação <span class="highlight">para fornecedores do governo</span></h1>
                <ul class="hero-features">
                    <li>Encontre licitações e acompanhe os resultados</li>
                    <li>Gerencie as licitações e todos os documentos</li>
                    <li>Assessoria cadastral e consultoria jurídica</li>
                    <li>Monitore o chat do pregão em tempo real</li>
                </ul>
                <button class="btn-primary">Cadastre-se gratuitamente</button>
            </div>
            <div class="hero-dashboard">
                <div class="dashboard-header">
                    <div class="icon">📊</div>
                    <span>conectar</span>
                </div>
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="icon">📋</div>
                        <h4>Boletins de Licitações</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">🔍</div>
                        <h4>Encontrar Licitações</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">📈</div>
                        <h4>Encontrar Acompanhamentos</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">⚙️</div>
                        <h4>Gerenciar Licitações</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">💬</div>
                        <h4>Monitorar Chat</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">📊</div>
                        <h4>Análise de Mercado</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">⚖️</div>
                        <h4>Licitações Estratégicas</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">⚖️</div>
                        <h4>Jurídico Fácil</h4>
                    </div>
                    <div class="dashboard-card">
                        <div class="icon">👥</div>
                        <h4>Assessoria Cadastral</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-container">
            <div class="features-intro">
                <div>
                    <h2>Tudo que você precisa para vender ao governo</h2>
                </div>
                <div>
                    <p>Simplificamos o processo desde a licitação até a execução do contrato.</p>
                </div>
            </div>

            <div class="benefits">
                <div class="benefit-card">
                    <div class="icon">👤</div>
                    <h3>Personalização</h3>
                    <p>Receba as licitações qualificadas para sua área de atuação.</p>
                </div>
                <div class="benefit-card">
                    <div class="icon">⚡</div>
                    <h3>Agilidade</h3>
                    <p>Automatize processos e não se preocupe mais com prazos.</p>
                </div>
                <div class="benefit-card">
                    <div class="icon">🔒</div>
                    <h3>Segurança</h3>
                    <p>Atue com mais confiança para não perder oportunidades.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tools Section -->
    <section class="tools">
        <div class="tools-container">
            <h2>Descubra como nossas ferramentas podem ajudar</h2>
            <p>Conheça cada uma das soluções disponíveis em nossa plataforma.</p>

            <div class="tools-grid">
                <div class="tool-card">
                    <div class="icon">📊</div>
                    <h3>Banco de dados</h3>
                    <p>Consulta às licitações com filtros avançados de busca, como pesquisa de palavras-chave no objeto e no edital.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">📅</div>
                    <h3>Boletins diários</h3>
                    <p>Avisos de novas licitações, licitações e resultados com envio por e-mail três vezes ao dia.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">📄</div>
                    <h3>Gestão de documentos</h3>
                    <p>Arquivo e gestão de toda a documentação necessária para a habilitação da empresa nas licitações.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">⭐</div>
                    <h3>Gerenciamento de licitações</h3>
                    <p>Organização e gestão das licitações com compartilhamento entre a equipe e acesso rápido às atualizações.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">💬</div>
                    <h3>Monitoramento de chat</h3>
                    <p>Acompanhamento de pregões eletrônicos com alertas sobre convocações e atualizações.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">🔍</div>
                    <h3>Análise de concorrentes</h3>
                    <p>Acesso às informações importantes sobre a concorrência, incluindo dados sobre sanções e penalidades.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">📈</div>
                    <h3>Análise de mercado</h3>
                    <p>Insights e análises detalhadas do mercado de licitações para otimizar suas estratégias de participação.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">⚖️</div>
                    <h3>Consultoria jurídica</h3>
                    <p>Suporte jurídico especializado para questões relacionadas a licitações e contratos públicos.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
                <div class="tool-card">
                    <div class="icon">👥</div>
                    <h3>Assessoria cadastral</h3>
                    <p>Auxílio completo no processo de cadastramento em órgãos públicos e plataformas de licitação.</p>
                    <a href="#" class="tool-link">saiba mais →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing">
        <div class="pricing-container">
            <h2>Escolha seu Plano</h2>
            <p>Encontre o plano perfeito para suas necessidades de licitação</p>

            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Super</h3>
                    <ul class="pricing-features">
                        <li>Boletins</li>
                        <li>Encontrar licitações</li>
                        <li>Encontrar acompanhamentos</li>
                        <li>Gerenciar licitações</li>
                        <li>Consulta Go</li>
                        <li>Análise de mercado</li>
                        <li>Concorrentes</li>
                        <li>Jurídico fácil</li>
                        <li>Suporte jurídico</li>
                        <li>Gerenciar Documentos</li>
                        <li class="unavailable">Monitorar chat</li>
                        <li class="unavailable">Licitações Estratégicas</li>
                        <li class="unavailable">Contratos</li>
                        <li class="unavailable">Ata de Registro de Preços</li>
                        <li class="unavailable">Pergunte ao Edital</li>
                        <li class="unavailable">Especialista Virtual</li>
                        <li class="unavailable">Dr. Licita</li>
                        <li class="unavailable">Robô de Lances</li>
                    </ul>
                    <button class="pricing-btn">Escolher Plano</button>
                </div>

                <div class="pricing-card">
                    <h3>Premium</h3>
                    <ul class="pricing-features">
                        <li>Boletins</li>
                        <li>Encontrar licitações</li>
                        <li>Encontrar acompanhamentos</li>
                        <li>Gerenciar licitações</li>
                        <li>Consulta Go</li>
                        <li>Análise de mercado</li>
                        <li>Concorrentes</li>
                        <li>Jurídico fácil</li>
                        <li>Suporte jurídico</li>
                        <li>Gerenciar Documentos</li>
                        <li>Monitorar chat</li>
                        <li>Licitações Estratégicas</li>
                        <li>Contratos</li>
                        <li>Ata de Registro de Preços</li>
                        <li>Pergunte ao Edital</li>
                        <li>Especialista Virtual</li>
                        <li class="unavailable">Dr. Licita</li>
                        <li class="unavailable">Robô de Lances</li>
                    </ul>
                    <button class="pricing-btn">Escolher Plano</button>
                </div>
				<div class="pricing-card">
                    <h3>Advanced</h3>
                    <ul class="pricing-features">
                        <li>Boletins</li>
                        <li>Encontrar licitações</li>
                        <li>Encontrar acompanhamentos</li>
                        <li>Gerenciar licitações</li>
                        <li>Consulta Go</li>
                        <li>Análise de mercado</li>
                        <li>Concorrentes</li>
                        <li>Jurídico fácil</li>
                        <li>Suporte jurídico</li>
                        <li>Gerenciar Documentos</li>
                        <li>Monitorar chat</li>
                        <li>Licitações Estratégicas</li>
                        <li>Contratos</li>
                        <li>Ata de Registro de Preços</li>
                        <li>Pergunte ao Edital</li>
                        <li>Especialista Virtual</li>
                        <li>Dr. Licita</li>
                        <li class="unavailable">Robô de Lances</li>
                    </ul>
                    <button class="pricing-btn">Escolher Plano</button>
                </div>
				<div class="pricing-card featured">
                    <h3>Black</h3>
                    <ul class="pricing-features">
                        <li>Boletins</li>
                        <li>Encontrar licitações</li>
                        <li>Encontrar acompanhamentos</li>
                        <li>Gerenciar licitações</li>
                        <li>Consulta Go</li>
                        <li>Análise de mercado</li>
                        <li>Concorrentes</li>
                        <li>Jurídico fácil</li>
                        <li>Suporte jurídico</li>
                        <li>Gerenciar Documentos</li>
                        <li>Monitorar chat</li>
                        <li>Licitações Estratégicas</li>
                        <li>Contratos</li>
                        <li>Ata de Registro de Preços</li>
                        <li>Pergunte ao Edital</li>
                        <li>Especialista Virtual</li>
                        <li>Dr. Licita</li>
						<li>Robô de Lances</li>
					</ul>
                    <button class="pricing-btn">Escolher Plano</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h3>HCL Licitações</h3>
                <p>Plataforma completa de licitações para fornecedores do governo. Conectando empresas às melhores oportunidades.</p>
            </div>

            <div class="footer-section">
                <h4>Produto</h4>
                <ul class="footer-links">
                    <li><a href="#">Planos</a></li>
                    <li><a href="#">Ferramentas</a></li>
                    <li><a href="#">Recursos</a></li>
                    <li><a href="#">Preços</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Suporte</h4>
                <ul class="footer-links">
                    <li><a href="#">Central de Ajuda</a></li>
                    <li><a href="#">Documentação</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Empresa</h4>
                <ul class="footer-links">
                    <li><a href="#">Sobre nós</a></li>
                    <li><a href="#">Carreiras</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 Conectar. Todos os direitos reservados.</p>
            <div class="social-links">
                <a href="#" aria-label="Facebook">f</a>
                <a href="#" aria-label="LinkedIn">in</a>
                <a href="#" aria-label="Instagram">📷</a>
                <a href="#" aria-label="Twitter">🐦</a>
            </div>
        </div>
    </footer>
@endsection
