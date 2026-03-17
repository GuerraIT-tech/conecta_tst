# Plano de migração Laravel -> Flask + React

## 1) Mapeamento de regras de negócio (Controllers)
No estado atual do repositório existe apenas `app/Http/Controllers/Controller.php` (classe base), sem controllers de domínio.

### Proposta de estrutura em Flask (Blueprints + Services)
- `app/blueprints/auth.py`: registro, login, endpoint `me` com JWT.
- `app/blueprints/users.py`: operações de usuário autenticado.
- `app/blueprints/companies.py`: leitura e manutenção de empresas.
- `app/blueprints/bids.py`: consulta de licitações e favoritos.
- `app/blueprints/radars.py`: radar e filtros por estado/modalidade.
- `app/services.py`: regras de negócio e acesso a dados para manter as rotas enxutas.

## 2) Autenticação
Sistema Laravel atual:
- Não há Sanctum/Passport/Breeze instalados em `composer.json`.
- A autenticação principal usa `Illuminate\Foundation\Auth\User` e `spatie/laravel-permission`.

Equivalente Flask adotado:
- `Flask-JWT-Extended` com endpoints:
  - `POST /api/auth/register`
  - `POST /api/auth/login`
  - `GET /api/auth/me`

## 3) Front-end React
Estrutura inicial em `frontend-react`:
- `src/types/models.ts`: interfaces TypeScript alinhadas aos modelos principais.
- `src/services/api.ts`: cliente Axios com interceptor JWT.
- `src/components/HomePage.tsx`: componente funcional com Tailwind e consumo de `/api/radars`.

## 4) Próximos passos recomendados
1. Implementar validações por schema (Pydantic/Marshmallow).
2. Adicionar Alembic para versionamento de schema no Flask.
3. Cobrir endpoints com testes (pytest + factory-boy).
4. Migrar telas restantes do Blade para componentes React por domínio.
