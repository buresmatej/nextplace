
# Nextplace

Nextplace is a travel logging platform where users can record and rate places they've visited around the world. Based on their ratings and travel history, an AI  analyzes their preferences and recommends new destinations tailored specifically to their taste making every next trip more personal than the last.

### Features
- Destination logging
- AI recommendations

### Tech stack
- PHP + Nette framework
- PostgreSQL
- Nextras ORM
- Docker

### Getting started
#### 1. Clone the repository
```bash
git clone https://github.com/buresmatej/nextplace.git
cd nextplace
```

#### 2. Set up environment variables
Create a `.env` file in the root of the project:
```env
OPENAI_API_KEY=your_openai_api_key
OPENAI_BASE_URL=your_openai_base_url
AI_MODEL=your_ai_model
PORT=your_port
```

#### 3. Build and run with Docker
```bash
docker-compose build
docker-compose up
```

The app should now be running at `http://localhost:PORT`.

## License
MIT
