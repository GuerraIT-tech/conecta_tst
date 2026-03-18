from flask import Blueprint, jsonify
from flask_jwt_extended import jwt_required

from app.services import UserService


users_bp = Blueprint("users", __name__, url_prefix="/api/users")


@users_bp.get("")
@jwt_required()
def list_users() -> tuple[list[dict], int]:
    users = UserService.list_active_users()
    return jsonify([{"id": user.id, "name": user.name, "email": user.email} for user in users]), 200
