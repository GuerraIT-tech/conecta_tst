from __future__ import annotations

from typing import Any

from werkzeug.security import check_password_hash, generate_password_hash

from app.extensions import db
from models import Bid, Company, Radar, User


class AuthService:
    @staticmethod
    def register_user(name: str, email: str, password: str, cnpj: str | None = None) -> User:
        user = User(name=name, email=email, cnpj=cnpj, password=generate_password_hash(password))
        db.session.add(user)
        db.session.commit()
        return user

    @staticmethod
    def authenticate(email: str, password: str) -> User | None:
        user = User.query.filter_by(email=email).first()
        if not user:
            return None
        return user if check_password_hash(user.password, password) else None


class UserService:
    @staticmethod
    def list_active_users() -> list[User]:
        return User.query.filter_by(is_active=True).all()


class BidService:
    @staticmethod
    def list_active_bids() -> list[Bid]:
        return Bid.query.filter_by(is_active=True).all()


class CompanyService:
    @staticmethod
    def list_active_companies() -> list[Company]:
        return Company.query.filter_by(is_active=True).all()


class RadarService:
    @staticmethod
    def list_radars(filters: dict[str, Any]) -> list[Radar]:
        query = Radar.query
        if filters.get("state_id"):
            query = query.filter(Radar.state_id == int(filters["state_id"]))
        if filters.get("modality_id"):
            query = query.filter(Radar.modality_id == int(filters["modality_id"]))
        return query.order_by(Radar.created_at.desc()).all()
