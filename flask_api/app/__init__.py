from __future__ import annotations

from flask import Flask

from app.blueprints.auth import auth_bp
from app.blueprints.bids import bids_bp
from app.blueprints.companies import companies_bp
from app.blueprints.radars import radars_bp
from app.blueprints.users import users_bp
from app.config import Config
from app.extensions import db, jwt


def create_app() -> Flask:
    app = Flask(__name__)
    app.config.from_object(Config)

    db.init_app(app)
    jwt.init_app(app)

    app.register_blueprint(auth_bp)
    app.register_blueprint(users_bp)
    app.register_blueprint(companies_bp)
    app.register_blueprint(bids_bp)
    app.register_blueprint(radars_bp)

    @app.get("/health")
    def health() -> tuple[dict[str, str], int]:
        return {"status": "ok"}, 200

    return app
