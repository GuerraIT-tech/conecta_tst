from __future__ import annotations

from collections.abc import Sequence
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
        return User.query.filter_by(is_active=True).order_by(User.name.asc()).all()


class BidService:
    @staticmethod
    def list_active_bids() -> list[Bid]:
        return Bid.query.filter_by(is_active=True).order_by(Bid.created_at.desc()).all()

    @staticmethod
    def create_bid(payload: dict[str, Any]) -> Bid:
        bid = Bid(
            bidding_modality=payload["bidding_modality"],
            bidding_number=payload["bidding_number"],
            requesting_agency=payload.get("requesting_agency"),
            registration_email=payload.get("registration_email"),
            auctioneer_name=payload.get("auctioneer_name"),
        )
        db.session.add(bid)
        db.session.commit()
        return bid

    @staticmethod
    def update_bid(bid_id: int, payload: dict[str, Any]) -> Bid:
        bid = Bid.query.get_or_404(bid_id)
        for field in ["bidding_modality", "bidding_number", "requesting_agency", "registration_email", "auctioneer_name"]:
            if field in payload:
                setattr(bid, field, payload.get(field))
        db.session.commit()
        return bid

    @staticmethod
    def delete_bid(bid_id: int) -> None:
        bid = Bid.query.get_or_404(bid_id)
        db.session.delete(bid)
        db.session.commit()


class CompanyService:
    @staticmethod
    def list_active_companies() -> list[Company]:
        return Company.query.filter_by(is_active=True).order_by(Company.created_at.desc()).all()

    @staticmethod
    def create_company(payload: dict[str, Any]) -> Company:
        company = Company(
            corporate_name=payload["corporate_name"],
            trade_name=payload.get("trade_name"),
            cnpj=payload.get("cnpj"),
            address=payload["address"],
            number=payload["number"],
            district=payload["district"],
            city=payload["city"],
            state=payload["state"],
            zip_code=payload["zip_code"],
            phone=payload["phone"],
            email=payload["email"],
        )
        db.session.add(company)
        db.session.commit()
        return company

    @staticmethod
    def update_company(company_id: int, payload: dict[str, Any]) -> Company:
        company = Company.query.get_or_404(company_id)
        for field in [
            "corporate_name",
            "trade_name",
            "cnpj",
            "address",
            "number",
            "district",
            "city",
            "state",
            "zip_code",
            "phone",
            "email",
        ]:
            if field in payload:
                setattr(company, field, payload.get(field))
        db.session.commit()
        return company

    @staticmethod
    def delete_company(company_id: int) -> None:
        company = Company.query.get_or_404(company_id)
        db.session.delete(company)
        db.session.commit()


class RadarService:
    @staticmethod
    def list_radars(filters: dict[str, Any]) -> list[Radar]:
        query = Radar.query
        if filters.get("state_id"):
            query = query.filter(Radar.state_id == int(filters["state_id"]))
        if filters.get("modality_id"):
            query = query.filter(Radar.modality_id == int(filters["modality_id"]))
        return query.order_by(Radar.created_at.desc()).all()


class ReportService:
    @staticmethod
    def build_dashboard() -> dict[str, Any]:
        total_clients = Company.query.count()
        active_bids = Bid.query.filter_by(is_active=True).count()
        total_users = User.query.count()
        radar_open = Radar.query.count()

        latest_clients: Sequence[Company] = Company.query.order_by(Company.created_at.desc()).limit(5).all()
        latest_bids: Sequence[Bid] = Bid.query.order_by(Bid.created_at.desc()).limit(5).all()
        monthly_labels = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun"]

        return {
            "kpis": {
                "total_clients": total_clients,
                "active_bids": active_bids,
                "total_users": total_users,
                "radar_items": radar_open,
            },
            "latest_clients": [
                {
                    "id": company.id,
                    "corporate_name": company.corporate_name,
                    "city": company.city,
                    "state": company.state,
                }
                for company in latest_clients
            ],
            "latest_bids": [
                {
                    "id": bid.id,
                    "bidding_number": bid.bidding_number,
                    "bidding_modality": bid.bidding_modality,
                    "requesting_agency": bid.requesting_agency,
                }
                for bid in latest_bids
            ],
            "charts": {
                "revenue_projection": {
                    "labels": monthly_labels,
                    "values": [12, 18, 24, 22, 31, 35],
                },
                "pipeline_by_status": {
                    "labels": ["Novas", "Em análise", "Propostas", "Ganhas"],
                    "values": [8, 12, 5, 3],
                },
            },
        }
