from __future__ import annotations

from flask import Blueprint, jsonify
from flask_jwt_extended import jwt_required

from app.services import ReportService


reports_bp = Blueprint("reports", __name__, url_prefix="/api/reports")


@reports_bp.get("/dashboard")
@jwt_required(optional=True)
def dashboard() -> tuple[dict, int]:
    return jsonify(ReportService.build_dashboard()), 200
