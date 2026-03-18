from __future__ import annotations

from flask import Blueprint, jsonify, request
from flask_jwt_extended import create_access_token, get_jwt_identity, jwt_required

from app.services import AuthService
from models import User


auth_bp = Blueprint("auth", __name__, url_prefix="/api/auth")


@auth_bp.post("/register")
def register() -> tuple[dict, int]:
    payload = request.get_json(force=True)
    required = ["name", "email", "password"]
    missing = [field for field in required if not payload.get(field)]
    if missing:
        return {"error": f"Campos obrigatórios ausentes: {', '.join(missing)}"}, 422

    user = AuthService.register_user(
        name=payload["name"],
        email=payload["email"],
        password=payload["password"],
        cnpj=payload.get("cnpj"),
    )
    return {"id": user.id, "email": user.email}, 201


@auth_bp.post("/login")
def login() -> tuple[dict, int]:
    payload = request.get_json(force=True)
    user = AuthService.authenticate(payload.get("email", ""), payload.get("password", ""))
    if not user:
        return {"error": "Credenciais inválidas"}, 401

    token = create_access_token(identity=str(user.id))
    return {"access_token": token}, 200


@auth_bp.get("/me")
@jwt_required()
def me() -> tuple[dict, int]:
    user_id = int(get_jwt_identity())
    user = User.query.get_or_404(user_id)
    return jsonify({"id": user.id, "name": user.name, "email": user.email, "cnpj": user.cnpj})
