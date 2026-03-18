from __future__ import annotations

from typing import Any

from flask import Blueprint, jsonify, request
from flask_jwt_extended import jwt_required

from app.services import CompanyService


companies_bp = Blueprint("companies", __name__, url_prefix="/api/companies")


def serialize_company(company: Any) -> dict[str, Any]:
    return {
        "id": company.id,
        "corporate_name": company.corporate_name,
        "trade_name": company.trade_name,
        "cnpj": company.cnpj,
        "city": company.city,
        "state": company.state,
        "email": company.email,
        "address": company.address,
        "number": company.number,
        "district": company.district,
        "zip_code": company.zip_code,
        "phone": company.phone,
    }


@companies_bp.get("", endpoint="list_companies")
@jwt_required(optional=True)
def list_companies_view() -> tuple[Any, int]:
    companies = CompanyService.list_active_companies()
    return jsonify([serialize_company(company) for company in companies]), 200


@companies_bp.post("", endpoint="create_company")
@jwt_required(optional=True)
def create_company_view() -> tuple[dict[str, Any], int]:
    payload = request.get_json(force=True)
    required = ["corporate_name", "address", "number", "district", "city", "state", "zip_code", "phone", "email"]
    missing = [field for field in required if not payload.get(field)]
    if missing:
        return {"error": f"Campos obrigatórios ausentes: {', '.join(missing)}"}, 422

    company = CompanyService.create_company(payload)
    return serialize_company(company), 201


@companies_bp.put("/<int:company_id>", endpoint="update_company")
@jwt_required(optional=True)
def update_company_view(company_id: int) -> tuple[dict[str, Any], int]:
    company = CompanyService.update_company(company_id, request.get_json(force=True))
    return serialize_company(company), 200


@companies_bp.delete("/<int:company_id>", endpoint="delete_company")
@jwt_required(optional=True)
def delete_company_view(company_id: int) -> tuple[dict[str, str], int]:
    CompanyService.delete_company(company_id)
    return {"status": "deleted"}, 200
