# Mudanças nas Restrições de Liga

## Alterações Realizadas

### Schema do Banco de Dados
- **Antes**: Palavra-chave (`keyword`) era única, nome da liga não tinha restrição única
- **Depois**: Nome da liga (`name`) é único, palavra-chave pode ser repetida

### Arquivos Modificados

1. **`db_schema.sql`**
   - Removida restrição `UNIQUE` da coluna `keyword`
   - Adicionada restrição `UNIQUE` na coluna `name`

2. **`migrate_leagues_constraints.sql`** (novo arquivo)
   - Script para migrar bancos existentes
   - Remove índice único da palavra-chave
   - Adiciona índice único no nome da liga

3. **`public/backend/league_logic/create_league.php`**
   - Removida verificação de palavra-chave duplicada
   - Alterada verificação para nome único globalmente
   - Removida validação de espaços na palavra-chave

4. **`public/leagues.php`**
   - Atualizada mensagem de ajuda no formulário
   - Removida validação JavaScript de espaços
   - Esclarecimento que múltiplas ligas podem usar a mesma palavra-chave

## Benefícios das Mudanças

1. **Flexibilidade**: Múltiplas ligas podem usar palavras-chave simples como "123" ou "senha"
2. **Usabilidade**: Palavras-chave podem conter espaços para maior clareza
3. **Organização**: Nomes de liga únicos evitam confusão
4. **Escalabilidade**: Menos restrições desnecessárias no sistema

## Como Aplicar as Mudanças

### Para Bancos Novos
Apenas execute o `db_schema.sql` atualizado.

### Para Bancos Existentes
Execute o script de migração:
```sql
mysql -u seu_usuario -p game_platform < migrate_leagues_constraints.sql
```

## Exemplo de Uso

Agora é possível ter:
- Liga "Amigos da Escola" com palavra-chave "escola123"
- Liga "Colegas do Trabalho" com palavra-chave "escola123" (mesma palavra-chave!)
- Liga "Família Silva" com palavra-chave "minha familia"

Mas não é possível ter duas ligas com o mesmo nome.
