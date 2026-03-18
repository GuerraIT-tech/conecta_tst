from flask import Blueprint, jsonify
from flask_jwt_extended import jwt_required

from app.services import CompanyService


companies_bp = Blueprint("companies", __name__, url_prefix="/api/companies")


@companies_bp.get("")
@jwt_required()
def list_companies() -> tuple[list[dict], int]:
    companies = CompanyService.list_active_companies()
    return jsonify([
        {"id": company.id, "corporate_name": company.corporate_name, "cnpj": company.cnpj}
        for company in companies
    ]), 200
