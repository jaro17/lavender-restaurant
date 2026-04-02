You are my senior PHP architect.

Context:
- I build small production websites for real clients
- Frontend is already built using Shuffle (HTML/CSS)
- I need the admin to be able to edit the weekly menu       themselves via the admin interface.
- Backend is Slim 4
- Slim is used as admin backend + light API
- Database: SQLite
- Admin uses simple Twig templates
- No overengineering

Goal:
Create a COMPLETE implementation plan before coding.

Your task:
1. Design simple architecture
2. Define:
   - folder structure
   - database schema
   - routes (admin + API)
   - modules (pages, settings, gallery, etc.)
3. Break everything into small ordered phases

Each phase must include:
- goal
- files involved
- what will be built
- how to test it
- done criteria

Constraints:
- PDO only
- session-based auth
- simple CRUD
- no heavy patterns
- no DDD / no CQRS / no microservices

Output format:
1. Project summary
2. Architecture
3. Folder structure
4. Database
5. Implementation phases
6. MVP
7. What to avoid
8. First step to implement

IMPORTANT:
Do NOT generate code.